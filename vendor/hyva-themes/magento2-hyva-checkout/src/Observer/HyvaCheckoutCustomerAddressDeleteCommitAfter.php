<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer;

use Exception;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Model\Address as CustomerAddressModel;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface as QuoteRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Psr\Log\LoggerInterface;

class HyvaCheckoutCustomerAddressDeleteCommitAfter implements ObserverInterface
{
    protected SessionCheckout $sessionCheckout;
    protected QuoteRepositoryInterface $quoteRepository;
    protected LoggerInterface $logger;

    public function __construct(
        SessionCheckout $sessionCheckout,
        QuoteRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            if ($quote->getCustomerIsGuest()) {
                return;
            }

            $addressShipping = $quote->getShippingAddress();
            $addressBilling = $quote->getBillingAddress();

            /** @var array<string, AddressInterface> $types */
            $types = array_filter([
                'shipping' => $addressShipping,
                'billing'  => $addressBilling,
            ], function (AddressInterface $value) use ($observer) {
                try {
                    return $value->getCustomerAddressId() === $observer->getData('customer_address')->getId();
                } catch (Exception $exception) {
                    $this->logger->warning($exception->getMessage(), ['exception' => $exception]);
                }

                return false;
            });

            if (! empty($types)) {
                if (isset($types['shipping'])) {
                    $addressShipping->setCustomerAddressId(null);
                }
                if (isset($types['billing'])) {
                    $addressBilling->setCustomerAddressId(null);
                }

                $this->quoteRepository->save($quote);
            }
        } catch (NoSuchEntityException | LocalizedException $exception) {
            $this->logger->warning($exception->getMessage(), ['exception' => $exception]);
        }
    }
}
