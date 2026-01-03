<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class EavAttributeField extends AbstractEntityField
{
    protected AttributeInterface $attribute;
    protected ScopeConfigInterface $scopeConfig;
    protected EavAttributeMappingConfigInterface $mappingConfig;

    public function __construct(FormFieldDependencies $context)
    {
        parent::__construct($context);

        $this->attribute = $context->getAttribute();
        $this->scopeConfig = $context->getScopeConfig();
        $this->mappingConfig = $context->getMappingConfig();
    }

    public function getDefaultValue()
    {
        return $this->attribute->getDefaultValue();
    }

    public function getId(): ?string
    {
        return $this->getConfig()->getAttributeCode() ?? parent::getId();
    }

    public function getName(): string
    {
        return $this->getConfig()->getAttributeCode() ?? parent::getName();
    }

    public function getAttribute(): AttributeInterface
    {
        return $this->attribute;
    }

    public function getAttributeCode(): string
    {
        return $this->getConfig()->getAttributeCodeAlias() ?? $this->getId();
    }

    public function isRequired(): bool
    {
        $isRequired = $this->getData(self::IS_REQUIRED);

        if ($isRequired === null) {
            return $this->getConfig()->getRequired();
        }

        return (bool) $isRequired;
    }

    public function getLabel(): string
    {
        // Parent data should always be leading. When not applied, move on with the attribute values.
        $label = $this->getData(self::LABEL) ?? $this->attribute->getDefaultFrontendLabel();

        if ($label === null) {
            $label = $this->attribute->getAttributeCode();
        }

        return ucfirst($label);
    }

    public function getFrontendInput(): string
    {
        if ($this->hasOptions()) {
            return parent::getFrontendInput();
        }

        $input = $this->getData(self::INPUT) ?? $this->attribute->getFrontendInput();
        return $input === 'multiline' ? 'text' : $input;
    }

    public function getTooltip(): string
    {
        return $this->getData(self::TOOLTIP) ?? $this->getConfig()->getTooltip();
    }

    public function getSortOrder(): int
    {
        return $this->getData(self::POSITION) ?? $this->getConfig()->getSortOrder();
    }

    public function isAutoSave(): bool
    {
        return $this->getData(self::IS_AUTO_SAVE) ?? $this->getConfig()->getAutoSave();
    }

    public function getConfig(): EavAttributeMappingConfigInterface
    {
        return $this->mappingConfig;
    }
}
