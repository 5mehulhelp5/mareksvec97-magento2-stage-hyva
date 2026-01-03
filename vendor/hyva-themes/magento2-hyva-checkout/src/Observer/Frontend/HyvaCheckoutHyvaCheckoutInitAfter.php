<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer\Frontend;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigBilling;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigExperimental;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Model\ResourceModel\Address\Collection as CustomAddressCollection;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Directory\Helper\Data as DirectoryDataHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\BillingAddressManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\Cart\ShippingMethod;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Quote\Model\ShippingMethodManagementInterface;
use Psr\Log\LoggerInterface;

class HyvaCheckoutHyvaCheckoutInitAfter implements ObserverInterface
{
    protected SessionCheckout $sessionCheckout;
    protected SessionCustomer $sessionCustomer;
    protected CartRepositoryInterface $quoteRepository;
    protected AddressInterfaceFactory $addressFactory;
    protected DirectoryDataHelper $directoryDataHelper;
    protected ShippingAddressManagementInterface $shippingAddressManagement;
    protected BillingAddressManagementInterface $billingAddressManagement;
    protected LoggerInterface $logger;
    protected SystemConfigBilling $coreConfigBilling;
    protected PaymentMethodManagementInterface $paymentMethodManagement;
    protected ShippingMethodManagementInterface $shippingMethodManagement;
    protected SystemConfigExperimental $systemConfigExperimental;

    public function __construct(
        SessionCheckout $sessionCheckout,
        SessionCustomer $sessionCustomer,
        CartRepositoryInterface $quoteRepository,
        AddressInterfaceFactory $addressFactory,
        DirectoryDataHelper $directoryDataHelper,
        ShippingAddressManagementInterface $shippingAddressManagement,
        BillingAddressManagementInterface $billingAddressManagement,
        LoggerInterface $logger,
        SystemConfigBilling $coreConfigBilling,
        ?PaymentMethodManagementInterface $paymentMethodManagement = null,
        ?ShippingMethodManagementInterface $shippingMethodManagement = null,
        ?SystemConfigExperimental $systemConfigExperimental = null
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->sessionCustomer = $sessionCustomer;
        $this->quoteRepository = $quoteRepository;
        $this->addressFactory  = $addressFactory;
        $this->directoryDataHelper = $directoryDataHelper;
        $this->shippingAddressManagement = $shippingAddressManagement;
        $this->billingAddressManagement = $billingAddressManagement;
        $this->logger = $logger;
        $this->coreConfigBilling = $coreConfigBilling;
        $this->paymentMethodManagement = $paymentMethodManagement
            ?: ObjectManager::getInstance()->get(PaymentMethodManagementInterface::class);
        $this->shippingMethodManagement = $shippingMethodManagement
            ?: ObjectManager::getInstance()->get(ShippingMethodManagementInterface::class);
        $this->systemConfigExperimental = $systemConfigExperimental
            ?: ObjectManager::getInstance()->get(SystemConfigExperimental::class);
    }

    /**
     * Define the customer/guest default addresses on checkout initialization.
     */
    public function execute(Observer $observer): void
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
        } catch (NoSuchEntityException | LocalizedException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            return;
        }

        if ($quote->isVirtual() === false) {
            $this->processShippingAddress($quote)->collectTotals();

            if ($this->systemConfigExperimental->enableFirstAvailableShippingMethod()) {
                $this->setFirstAvailableShippingMethod($quote);
            }
        }

        if ($this->systemConfigExperimental->enableFirstAvailablePaymentMethod()) {
            $this->setFirstAvailablePaymentMethod($quote);
        }

        $this->processBillingAddress($quote);
    }

    /**
     * Making sure we have a valid shipping address on checkout initialization.
     */
    public function processShippingAddress(Quote $quote): Quote
    {
        $addressShipping = $quote->getShippingAddress();

        if ($addressShipping->validate() !== true && $this->sessionCustomer->isLoggedIn()) {
            $addressShippingPrimary = $this->sessionCustomer->getCustomer()->getPrimaryShippingAddress();

            if ($addressShippingPrimary === false) {
                $customerAdditionalAddressList = $this->getAdditionalAddresses();
                if ($customerAdditionalAddressList->getSize() !== 0) {
                    $addressShippingPrimary = $customerAdditionalAddressList->getFirstItem();
                    $addressShipping->importCustomerAddressData($this->addressFactory->create([
                        'data' => $addressShippingPrimary->getData()
                    ]));
                    $addressShipping->setAddressType(Quote\Address::ADDRESS_TYPE_SHIPPING);
                    $addressShipping->setCustomerAddressId($addressShippingPrimary->getId());
                }
            }
        }

        if ($addressShipping->getCountryId() === null) {
            $addressShipping->setCountryId($this->directoryDataHelper->getDefaultCountry());
        }

        $canApplyBillingAsShippingAddress = $this->coreConfigBilling->canApplyShippingAsBillingAddress();

        // Override config setting for logged-in users based on their actual addresses
        if ($this->sessionCustomer->isLoggedIn()) {
            $customer = $this->sessionCustomer->getCustomer();
            $billingAddress = $customer->getPrimaryBillingAddress();
            $shippingAddress = $customer->getPrimaryShippingAddress();

            // Determine the address scenario and handle accordingly
            if ($billingAddress && $shippingAddress) {
                // Compare the two addresses to see if they're the same
                $canApplyBillingAsShippingAddress = ((int) $billingAddress->getId() === (int) $shippingAddress->getId());
            }

            if ($billingAddress && !$shippingAddress) {
                // Don't pre-check the box so billing address shows properly
                $canApplyBillingAsShippingAddress = false;
            }
        }

        $addressShipping->setSameAsBilling((int) $canApplyBillingAsShippingAddress);

        try {
            $this->shippingAddressManagement->assign($quote->getId(), $addressShipping);
        } catch (InputException | NoSuchEntityException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        return $quote;
    }

    /**
     * Making sure we have a valid billing address on checkout initialization.
     */
    public function processBillingAddress(Quote $quote): Quote
    {
        if ($quote->isVirtual()) {
            return $this->processBillingAddressForVirtualCart($quote);
        }

        $addressShipping = $quote->getShippingAddress();
        $addressBilling = $quote->getBillingAddress();

        if ((bool) $addressShipping->getSameAsBilling() && $addressShipping->getCustomerAddressId() !== $addressBilling->getCustomerAddressId()) {
            $addressBilling->setCustomerAddressId($addressShipping->getCustomerAddressId());
        }

        if ($addressBilling->getCountryId() === null && ! $addressBilling->getCustomerAddressId()) {
            $addressBilling->setCountryId($this->directoryDataHelper->getDefaultCountry());
        }

        $this->quoteRepository->save($quote);
        return $quote;
    }

    /**
     * Making sure a correct billing address for a virtual cart is available.
     */
    public function processBillingAddressForVirtualCart(Quote $quote): Quote
    {
        $addressBilling = $quote->getBillingAddress();

        if ($addressBilling->validate() !== true && $this->sessionCustomer->isLoggedIn()) {
            $addressBillingPrimary = $this->sessionCustomer->getCustomer()->getPrimaryBillingAddress();

            if ($addressBillingPrimary === false) {
                $customerAdditionalAddressList = $this->sessionCustomer->getCustomer()->getAdditionalAddresses();

                if (count($customerAdditionalAddressList) !== 0) {
                    $addressBillingPrimary = $customerAdditionalAddressList[0];
                }
            }
            if ($addressBillingPrimary) {
                $addressBilling->importCustomerAddressData($this->addressFactory->create([
                    'data' => $addressBillingPrimary->getData()
                ]));
                $addressBilling->setCustomerAddressId($addressBillingPrimary->getId());
            }

            $addressBilling->setAddressType(Quote\Address::ADDRESS_TYPE_BILLING);
        }

        if ($addressBilling->getCountryId() === null) {
            $addressBilling->setCountryId($this->directoryDataHelper->getDefaultCountry());
        }

        $this->quoteRepository->save($quote);
        return $quote;
    }

    /**
     * Set first available shipping method as checked / active.
     */
    private function setFirstAvailableShippingMethod(Quote $quote): void
    {
        $shippingMethods = $this->shippingMethodManagement->estimateByExtendedAddress(
            $quote->getId(),
            $quote->getShippingAddress()
        );

        /** @var ShippingMethod $shippingMethod */
        foreach ($shippingMethods as $shippingMethod) {
            if ($shippingMethod->getErrorMessage()) {
                continue;
            }

            try {
                $this->shippingMethodManagement->set(
                    $quote->getId(),
                    $shippingMethod->getCarrierCode(),
                    $shippingMethod->getMethodCode()
                );
            } catch (NoSuchEntityException | LocalizedException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }

            break;
        }
    }

    /**
     * Set first available payment method as checked / active.
     */
    private function setFirstAvailablePaymentMethod(Quote $quote): void
    {
        try {
            $paymentMethods = $this->paymentMethodManagement->getList($quote->getId());

            foreach ($paymentMethods as $method) {
                if (!$method->isActive() || !$method->isAvailable($quote)) {
                    continue;
                }

                $quote->getPayment()->setMethod($method->getCode());
                break;
            }
        } catch (NoSuchEntityException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
    }

    private function getAdditionalAddresses(): CustomAddressCollection
    {
        return $this->sessionCustomer->getCustomer()->getAddressesCollection();
    }
}
