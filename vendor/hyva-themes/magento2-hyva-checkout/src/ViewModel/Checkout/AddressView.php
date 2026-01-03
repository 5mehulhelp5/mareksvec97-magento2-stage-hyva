<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout;

use Hyva\Checkout\Exception\CheckoutException;
use Hyva\Checkout\Model\Component\AddressTypeInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

abstract class AddressView implements ArgumentInterface
{
    protected SessionCheckout $sessionCheckout;
    protected AddressTypeInterface $addressType;
    protected LoggerInterface $logger;

    public function __construct(
        SessionCheckout $sessionCheckout,
        AddressTypeInterface $addressType,
        LoggerInterface $logger
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->addressType = $addressType;
        $this->logger = $logger;
    }

    public function renderView(): string
    {
        try {
            if ($view = $this->addressType->getComponentViewBlock()) {
                return $view->toHtml();
            }

            if ((bool) $this->sessionCheckout->getQuote()->getCustomerIsGuest()) {
                return $this->addressType->getFormBlock()->toHtml();
            }

            $customerAddressList = $this->addressType->getCustomerAddressList();

            if ($customerAddressList->getTotalCount() !== 0) {
                return $this->addressType->getAddressListBlock()->toHtml();
            }
        } catch (NoSuchEntityException | LocalizedException | CheckoutException $exception) {
            $this->logger->critical(
                'Something went wrong while rendering the address view: ' . $exception->getMessage(),
                ['exception' => $exception]
            );

            return 'Something went wrong while rendering.';
        }

        return $this->addressType->getFormBlock()->toHtml();
    }
}
