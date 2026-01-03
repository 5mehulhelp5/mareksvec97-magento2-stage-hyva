<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magewirephp\Magewire\Component;
use Psr\Log\LoggerInterface;

class BillingDetails extends Component
{
    public bool $billingAsShipping = false;

    protected CartRepositoryInterface $quoteRepository;
    protected SessionCheckout $sessionCheckout;
    protected SessionCustomer $sessionCustomer;
    protected Quote\AddressFactory $quoteAddressFactory;
    protected LoggerInterface $logger;

    protected $loader = true;

    protected $listeners = [
        'billing_address_submitted' => 'refresh',
        'shipping_address_submitted' => 'refresh',
        'shipping_address_activated' => 'refresh'
    ];

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        SessionCheckout $sessionCheckout,
        SessionCustomer $sessionCustomer,
        Quote\AddressFactory $quoteAddressFactory,
        LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->sessionCheckout = $sessionCheckout;
        $this->sessionCustomer = $sessionCustomer;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->logger = $logger;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function boot(): void
    {
        $addressShipping = $this->sessionCheckout->getQuote()->getShippingAddress();
        $this->billingAsShipping = (bool) $addressShipping->getSameAsBilling();
    }

    public function updatedBillingAsShipping(bool $value): bool
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            $addressShipping = $quote->getShippingAddress();
            $addressShipping->setSameAsBilling($value);

            $quote = $this->toggleBillingAsShipping($quote, $value);
            $this->quoteRepository->save($quote);
            
            $this->emit('billing_as_shipping_address_updated', [
                'billingAsShipping' => $this->billingAsShipping
            ]);
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving your billing preferences.');
            $value = ! $value;
        }

        return $value;
    }

    public function toggleBillingAsShipping(Quote $quote, bool $value): Quote
    {
        $address = $quote->getShippingAddress();

        if ($value === false) {
            $addressBillingPrimary = $this->sessionCustomer->getCustomer()->getPrimaryBillingAddress();

            if ($addressBillingPrimary) {
                $quote->getBillingAddress()->setCustomerAddressId($addressBillingPrimary->getId());
                return $quote;
            }

            // Handover the shipping address object for later usage.
            $addressShipping = $address;

            $address = $this->quoteAddressFactory->create();
            $address->importCustomerAddressData($addressShipping->exportCustomerAddress());
            $address->setCustomerAddressId($addressShipping->getCustomerAddressId());
        }

        $quote->getBillingAddress()
              ->importCustomerAddressData($address->exportCustomerAddress())
              ->setCustomerAddressId($address->getCustomerAddressId());

        return $quote;
    }

    /**
     * @deprecated has been replaced with the toggleBillingAsShipping method.
     * @see BillingDetails::toggleBillingAsShipping()
     */
    public function unsetBillingAsShipping(Quote $quote, Address $address)
    {
        $addressBillingPrimary = $this->sessionCustomer->getCustomer()->getPrimaryBillingAddress();

        $quote->getBillingAddress()
              ->importCustomerAddressData($address->exportCustomerAddress())
              ->setCustomerAddressId($addressBillingPrimary
                  ? $addressBillingPrimary->getId()
                  : $address->getCustomerAddressId());
    }
}
