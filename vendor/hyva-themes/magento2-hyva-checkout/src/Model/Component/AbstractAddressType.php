<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Component;

use Hyva\Checkout\Model\Form\EntityFormProviderInterface;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigShipping;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigBilling;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressSearchResultsInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;

abstract class AbstractAddressType extends AbstractExtensibleModel implements AddressTypeInterface
{
    protected SessionCheckout $sessionCheckout;
    protected AddressRepositoryInterface $addressRepository;
    protected AddressFactory $addressFactory;
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    protected FilterBuilder $filterBuilder;
    protected LayoutInterface $layout;
    protected EntityFormProviderInterface $entityFormProvider;
    protected SystemConfigShipping $systemConfigShipping;
    protected SystemConfigBilling $systemConfigBilling;

    /** @deprecated variable $systemConfigShipping should be used instead. */
    protected SystemConfigShipping $systemConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        SessionCheckout $sessionCheckout,
        AddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        LayoutInterface $layout,
        EntityFormProviderInterface $entityFormProvider,
        SystemConfigShipping $systemConfig,
        SystemConfigBilling $systemConfigBilling,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);

        $this->sessionCheckout = $sessionCheckout;
        $this->addressRepository = $addressRepository;
        $this->searchCriteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->layout = $layout;
        $this->entityFormProvider = $entityFormProvider;
        $this->systemConfigShipping = $systemConfig;
        $this->systemConfigBilling = $systemConfigBilling;

        $this->systemConfig = $systemConfig;
    }

    abstract public function getComponentViewBlock();

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCustomerAddressList(): AddressSearchResultsInterface
    {
        return $this->addressRepository->getList(
            $this->searchCriteriaBuilder->addFilter('parent_id', $this->sessionCheckout->getQuote()->getCustomerId())->create()
        );
    }

    public function getFormBlock()
    {
        return $this->layout->getBlock(sprintf(self::VIEW_ADDRESS_FORM, $this));
    }

    public function getAddressListBlock()
    {
        return $this->layout->getBlock(sprintf(self::VIEW_ADDRESS_LIST, $this));
    }

    public function getCustomerAddressListBlockType(): string
    {
        /**
         * @doc narrowed down to only shipping & billing address since at the time these were
         *      the only two address types. More could be added in the future, but we want
         *      to have no breaking backward compatible changes.
         */
        switch ($this->getNamespace()) {
            case AddressTypeShipping::TYPE:
                return $this->getShippingConfig()->getAddressListView();
            case AddressTypeBilling::TYPE:
                return $this->getBillingConfig()->getAddressListView();
            default:
                return '';
        }
    }

    public function getAddressRenderBlock()
    {
        return $this->layout->getBlock(sprintf(self::VIEW_ADDRESS, $this));
    }

    public function getFormModalShowEvent(): string
    {
        return sprintf('%s address-form-modal-show', $this);
    }

    public function getFormModalHideEvent(): string
    {
        return sprintf('%s address-form-modal-hide', $this);
    }

    public function __toString(): string
    {
        return $this->getNamespace();
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(AddressTypeExtensionInterface $extensionAttributes): AddressTypeInterface
    {
        $this->_setExtensionAttributes($extensionAttributes);
        return $this;
    }

    private function getShippingConfig(): SystemConfigShipping
    {
        return $this->systemConfigShipping;
    }

    private function getBillingConfig(): SystemConfigBilling
    {
        return $this->systemConfigBilling;
    }
}
