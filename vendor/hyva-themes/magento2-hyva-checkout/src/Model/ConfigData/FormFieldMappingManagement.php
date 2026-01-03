<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData;

use Hyva\Checkout\Block\Adminhtml\System\Config\HyvaThemesCheckout;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Checkout as SystemCheckoutConfig;
use Hyva\Checkout\Model\Form\EntityField\EavAttributeMappingConfigInterface;
use Hyva\Checkout\Model\Form\EntityField\EavAttributeMappingConfigInterfaceFactory;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class FormFieldMappingManagement
{
    protected SystemCheckoutConfig $systemCheckoutConfig;
    protected AttributeManagementInterface $attributeManagement;
    protected EavAttributeMappingConfigInterfaceFactory $eavAttributeMappingConfigInterfaceFactory;
    protected array $customFormFieldAttributeTypes;

    public function __construct(
        SystemCheckoutConfig $systemCheckoutConfig,
        AttributeManagementInterface $attributeManagement,
        EavAttributeMappingConfigInterfaceFactory $eavAttributeMappingConfigInterfaceFactory,
        array $customFormFieldAttributeTypes = []
    ) {
        $this->systemCheckoutConfig = $systemCheckoutConfig;
        $this->attributeManagement = $attributeManagement;
        $this->customFormFieldAttributeTypes = $customFormFieldAttributeTypes;
        $this->eavAttributeMappingConfigInterfaceFactory = $eavAttributeMappingConfigInterfaceFactory;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getShippingFormFieldMapping(): array
    {
        return $this->convertMapping($this->systemCheckoutConfig->getShippingEavAttributeFormFieldsMapping());
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getBillingAddressMapping(): array
    {
        return $this->convertMapping($this->systemCheckoutConfig->getBillingEavAttributeFormFieldsMapping());
    }

    /**
     * @return array<array<EavAttributeMappingConfigInterface, AttributeInterface>>
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function convertMapping($fields): array
    {
        $mapping = [];
        $attributes = $this->getAddressAttributes();

        foreach ($fields as $field) {
            $field[EavAttributeMappingConfigInterface::ATTRIBUTE_CODE_ALIAS] = $field[EavAttributeMappingConfigInterface::ATTRIBUTE_CODE];
            $field[EavAttributeMappingConfigInterface::ATTRIBUTE_CODE] = strtok($field[EavAttributeMappingConfigInterface::ATTRIBUTE_CODE_ALIAS], '.');

            // Map form field objects as long as the field is enabled.
            if (isset($attributes[$field[EavAttributeMappingConfigInterface::ATTRIBUTE_CODE]]) && $field[EavAttributeMappingConfigInterface::ENABLED] !== '0') {
                // Tooltip config value was added later, we need to make sure it is set.
                if (! isset($field[EavAttributeMappingConfigInterface::TOOL_TIP])) {
                    $field[EavAttributeMappingConfigInterface::TOOL_TIP] = '';
                }

                // Auto save config value was added in later release, we need to make sure it is set.
                if (! isset($field[EavAttributeMappingConfigInterface::AUTO_SAVE])) {
                    // Auto save is defaulted to false, If no config is present.
                    $field[EavAttributeMappingConfigInterface::AUTO_SAVE] = false;
                }

                $mapping[] = [
                    'mappingConfig' => $this->eavAttributeMappingConfigInterfaceFactory->create(['data' => $field]),
                    'attribute' => $attributes[$field[EavAttributeMappingConfigInterface::ATTRIBUTE_CODE]]
                ];
            }
        }

        return $mapping;
    }

    /**
     * @return AttributeInterface[]
     * @throws NoSuchEntityException
     */
    public function getAddressAttributes(): array
    {
        $result = [];

        $eavAttributesAddress = $this->attributeManagement->getAttributes(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS
        );
        $eavAttributesCustomer = $this->attributeManagement->getAttributes(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER
        );

        foreach ($eavAttributesAddress + $eavAttributesCustomer as $attribute) {
            if (! (bool) $attribute->getIsRequired() && in_array($attribute->getAttributeCode(), HyvaThemesCheckout::DISPENSABLE_ATTRIBUTES, true)) {
                continue;
            }

            $result[$attribute->getAttributeCode()] = $attribute;
        }

        return $result;
    }
}
