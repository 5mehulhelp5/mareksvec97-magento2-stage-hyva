<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView\BillingDetails;

use Hyva\Checkout\Exception\CheckoutException;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Component\AddressTypeManagement;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\BillingAddressManagementInterface;
use Magewirephp\Magewire\Component;
use Psr\Log\LoggerInterface;

class AddressList extends Component
{
    /** @var int|null $activeAddressEntity */
    public ?int $activeAddressEntity = 0;

    protected $listeners = [
        'billing_address_submitted' => 'refresh',
        'customer_shipping_address_saved' => 'refresh',
    ];

    protected $loader = [
        'activeAddressEntity' => 'Switching address'
    ];

    protected CartRepositoryInterface $quoteRepository;
    protected EvaluationResultFactory $evaluationResultFactory;
    protected SessionCheckout $sessionCheckout;
    protected BillingAddressManagementInterface $billingAddressManagement;
    protected LoggerInterface $logger;
    protected AddressTypeManagement $addressTypeManagement;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        EvaluationResultFactory $evaluationResultFactory,
        SessionCheckout $sessionCheckout,
        BillingAddressManagementInterface $billingAddressManagement,
        LoggerInterface $logger,
        AddressTypeManagement $addressTypeManagement
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->evaluationResultFactory = $evaluationResultFactory;
        $this->sessionCheckout = $sessionCheckout;
        $this->billingAddressManagement = $billingAddressManagement;
        $this->logger = $logger;
        $this->addressTypeManagement = $addressTypeManagement;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function boot(): void
    {
        $quote = $this->sessionCheckout->getQuote();
        $addressBillingCustomerId = $quote->getBillingAddress()->getCustomerAddressId();

        if ($addressBillingCustomerId !== null) {
            $this->activeAddressEntity = (int) $addressBillingCustomerId;
        }
    }

    public function activateAddress($id): bool
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            if ($quote->getCustomerIsGuest()) {
                throw new CheckoutException(__('You must login or register to activate an address.'));
            }

            $addressTypeShipping = $this->addressTypeManagement->getAddressTypeShipping();
            $addressTypeBilling = $this->addressTypeManagement->getAddressTypeBilling();

            $addressShipping = $addressTypeShipping->getQuoteAddress();
            $addressBilling = $addressTypeBilling->getQuoteAddress();

            if (! $quote->isVirtual() && $addressShipping->getSameAsBilling() && $addressShipping->getCustomerAddressId() !== $id) {
                throw new CheckoutException(__('The billing address is locked by the shipping address.'));
            }

            $availableCustomerAddresses = array_values(array_filter($quote->getCustomer()->getAddresses(), function ($address) use ($id) {
                return (int) $address->getId() === (int) $id;
            }));

            if (count($availableCustomerAddresses) === 0) {
                throw new NoSuchEntityException(__('This address does not belong to you. Please try again'));
            }

            $addressBillingNew = $addressBilling->importCustomerAddressData($availableCustomerAddresses[0]);

            $quote->setBillingAddress($addressBillingNew);
            $this->quoteRepository->save($quote);

            // Assign new customer address entity as the active one.
            $this->activeAddressEntity = (int) $addressBillingNew->getCustomerAddressId();

            $this->emit('billing_address_activated', [
                'id' => $this->activeAddressEntity
            ]);

            return true;
        } catch (CheckoutException $exception) {
            $this->dispatchErrorMessage($exception->getMessage());
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage(__('Something went wrong while activating the address.'));
        }

        return false;
    }

    public function updatingActiveAddressEntity(string $id): int
    {
        if ($this->activateAddress($id)) {
            return (int) $id;
        }

        return (int) $this->activeAddressEntity;
    }
}
