<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Component;

use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Component\AddressTypeExtensionInterface;
use Magento\Customer\Api\Data\AddressSearchResultsInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Quote\Model\Quote\Address;

interface AddressTypeInterface extends ExtensibleDataInterface
{
    public const VIEW_ADDRESS_FORM = 'checkout.%s-details.address-form';
    public const VIEW_ADDRESS_LIST  = 'checkout.%s-details.address-list';
    public const VIEW_ADDRESS  = 'checkout.%s-details.address';

    /**
     * Returns the current active address.
     */
    public function getQuoteAddress(): Address;

    /**
     * Returns the belonging child view Block alias/name which should be shown.
     *
     * @return BlockInterface|false
     */
    public function getComponentViewBlock();

    /**
     * Returns all available addresses for the current logged in customer.
     *
     * @return AddressSearchResultsInterface
     */
    public function getCustomerAddressList(): AddressSearchResultsInterface;

    /**
     * @return false|AbstractBlock
     */
    public function getFormBlock();

    /**
     * @return false|BlockInterface
     */
    public function getAddressListBlock();

    /**
     * @return string
     */
    public function getCustomerAddressListBlockType(): string;

    /**
     * @return false|BlockInterface
     */
    public function getAddressRenderBlock();

    /**
     * @return EntityFormInterface
     */
    public function getForm(): EntityFormInterface;

    /**
     * Prints the type namespace.
     */
    public function __toString(): string;

    public function getNamespace(): string;

    /**
     * @return \Hyva\Checkout\Model\Component\AddressTypeExtensionInterface|null
     */
    public function getExtensionAttributes();

    public function setExtensionAttributes(AddressTypeExtensionInterface $extensionAttributes): AddressTypeInterface;
}
