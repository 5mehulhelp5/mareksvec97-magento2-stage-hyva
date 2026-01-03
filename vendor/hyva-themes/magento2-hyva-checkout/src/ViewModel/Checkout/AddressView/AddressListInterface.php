<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\AddressView;

use Magento\Customer\Api\Data\AddressInterface;

interface AddressListInterface
{
    /**
     * @return AddressInterface[]
     */
    public function getAddressListItems(): array;

    public function getShowModalEvent(): string;

    public function getHideModalEvent(): string;

    public function renderAddress(AddressInterface $address, string $code): string;

    public function renderEntityName($address, string $prefix): string;

    /**
     * Returns the address form modal child block name.
     */
    public function getModalAddressFormBlockName(): string;

    /**
     * Returns the block alias of the belonging renderer type.
     */
    public function getRendererTypeAlias(array $items): string;

    /**
     * Returns if the customer is allowed to create additional addresses.
     */
    public function canCreateAddresses(): bool;

    /**
     * Returns the address type as string.
     */
    public function getTypeNamespace(): string;
}
