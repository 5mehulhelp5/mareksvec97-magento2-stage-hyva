<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer\Frontend;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface as QuoteRepositoryInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Psr\Log\LoggerInterface;

class HyvaCheckoutMagewireShippingAddressActivated implements ObserverInterface
{
    protected SessionCheckout $sessionCheckout;
    protected ShippingMethodManagementInterface $shippingMethodManagement;
    protected LoggerInterface $logger;
    protected QuoteRepositoryInterface $quoteRepository;

    public function __construct(
        SessionCheckout $sessionCheckout,
        ShippingMethodManagementInterface $shippingMethodManagement,
        QuoteRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $addressShippingEntity = $observer->getData('id');
            $addressShipping = $quote->getShippingAddress();

            if ($addressShippingEntity === null || $addressShippingEntity === $addressShipping->getId() || empty($addressShipping->getShippingMethod())) {
                return;
            }

            $availableShippingMethods = array_filter($this->shippingMethodManagement->getList($quote->getId()), static function ($method) use ($addressShipping) {
                return $method->getCarrierCode() . '_' . $method->getMethodCode() === $addressShipping->getShippingMethod();
            });

            // Un-sign the shipping method quote when the current one is no longer applicable.
            if (count($availableShippingMethods) === 0) {
                $quote->getShippingAddress()->setShippingMethod('');
                $this->quoteRepository->save($quote);
            }
        } catch (NoSuchEntityException | LocalizedException $exception) {
            $this->logger->notice(
                sprintf('Could not validate if the shipping method is still applicable: %s', $exception->getMessage()),
                ['exception' => $exception]
            );
        }
    }
}
