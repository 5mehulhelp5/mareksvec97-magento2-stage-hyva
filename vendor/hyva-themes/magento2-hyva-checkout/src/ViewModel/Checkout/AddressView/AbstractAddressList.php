<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\AddressView;

use Hyva\Checkout\Model\Component\AddressTypeInterface;
use Hyva\Checkout\ViewModel\Checkout\AddressRenderer;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractAddressList implements ArgumentInterface, AddressListInterface
{
    protected SessionCustomer $sessionCustomer;
    protected AddressRenderer $addressRenderer;
    protected LoggerInterface $logger;
    protected AddressTypeInterface $addressType;

    public function __construct(
        SessionCustomer $sessionCustomer,
        AddressRenderer $addressRenderer,
        LoggerInterface $logger,
        AddressTypeInterface $addressType
    ) {
        $this->sessionCustomer = $sessionCustomer;
        $this->addressRenderer = $addressRenderer;
        $this->logger = $logger;
        $this->addressType = $addressType;
    }

    public function getAddressListItems(): array
    {
        $list = [];

        try {
            $quoteAddress = $this->addressType->getQuoteAddress();

            $customerAddress = $quoteAddress->exportCustomerAddress();
            $customerAddress->setId($quoteAddress->getCustomerAddressId());

            $list[] = $customerAddress;

            // Let's leave in the quote address when it's not an address book entity.
            if ($quoteAddress->getCustomerAddressId() !== null) {
                $list = [];
            }

            $customerAddressList = $this->addressType->getCustomerAddressList();

            if ($customerAddressList->getTotalCount() !== 0) {
                $list = array_merge($customerAddressList->getItems(), $list);
            }
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        return $list;
    }

    public function getShowModalEvent(): string
    {
        return sprintf('modal:show:%s-checkout-modal-address-form', $this->addressType);
    }

    public function getHideModalEvent(): string
    {
        return sprintf('modal:hide:%s-checkout-modal-address-form', $this->addressType);
    }

    public function renderAddress(AddressInterface $address, string $code = 'html'): string
    {
        return $this->addressRenderer->renderCustomerAddress($address, $code);
    }

    public function renderEntityName($address, string $prefix = 'address'): string
    {
        return hash('sha256', implode('_', [$prefix, $this->addressType, $address->getId()]));
    }

    public function getModalAddressFormBlockName(): string
    {
        return sprintf('checkout.%s-details.address-list.form', $this->addressType);
    }

    public function getRendererTypeAlias(array $items): string
    {
        return $this->addressType->getCustomerAddressListBlockType();
    }

    /**
     * TODO implementation should be more centralized in the a future
     *      update because multiple features are depending on it.
     */
    public function canCreateAddresses(): bool
    {
        return $this->sessionCustomer->isLoggedIn();
    }

    public function getTypeNamespace(): string
    {
        return $this->addressType->__toString();
    }
}
