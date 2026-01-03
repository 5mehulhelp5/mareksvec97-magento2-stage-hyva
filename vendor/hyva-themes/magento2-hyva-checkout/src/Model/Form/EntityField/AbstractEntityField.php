<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

use Hyva\Checkout\Model\Form\AbstractEntityFormElement;
use Hyva\Checkout\Model\Form\EntityFieldConfigInterface;
use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFieldSelectInterface;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;

abstract class AbstractEntityField extends AbstractEntityFormElement implements EntityFieldInterface, EntityFieldSelectInterface
{
    protected EntityFieldConfigInterface $config;

    public function __construct(FormFieldDependencies $context, array $data = [])
    {
        parent::__construct($context, $data);

        $this->renderer = $context->getRenderer();
        $this->config = $context->getFieldConfig();
    }

    public function getId(): ?string
    {
        return $this->getData(EntityFormElementInterface::ID);
    }

    public function setValue($value, bool $canUseDefault = true): self
    {
        // Determine if it should fall back onto a default value on null.
        $value ??= $canUseDefault ? $this->getDefaultValue() : $value;

        $this->setData('value', is_string($value) ? trim($value) : $value);
        return $this;
    }

    public function setData($key, $value = null)
    {
        if ($key === 'value') {
            $previousValue = $this->getPreviousValue();

            if ($this->getValue() !== null && $previousValue === null) {
                $this->setData('previous_value', $this->getValue());
            }
        }

        parent::setData($key, $value);

        return $this;
    }

    public function getLabel(): string
    {
        return ucfirst(parent::getLabel());
    }

    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    public function hasValue(): bool
    {
        $value = $this->getValue();

        return is_string($value) ? strlen($value) !== 0 : $value !== null;
    }

    public function hasNoValue(): bool
    {
        return ! $this->hasValue();
    }

    public function getPreviousValue()
    {
        return $this->getData(self::PREVIOUS_VALUE);
    }

    public function empty(): self
    {
        $this->setValue('');

        /** @var EntityFieldInterface $relative */
        foreach ($this->getRelatives() as $relative) {
            $relative->empty();
        }

        return $this;
    }

    public function reset(): EntityFieldInterface
    {
        $this->setValue(null);

        /** @var EntityFieldInterface $relative */
        foreach ($this->getRelatives() as $relative) {
            $relative->reset();
        }

        return $this;
    }

    public function clear(): AbstractEntityField
    {
        $this->setValue(null, false);

        /** @var AbstractEntityField $relative */
        foreach ($this->getRelatives() as $relative) {
            $relative->clear();
        }

        return $this;
    }

    public function getFrontendInput(): string
    {
        if ($this->hasOptions()) {
            return 'select';
        }

        return $this->getData(self::INPUT) ?? $this->config->getDefaultFrontendInput();
    }

    public function getLayoutAlias(): string
    {
        return $this->getData(self::INPUT_ALIAS)
            ?? $this->getData('layout_alias')
            ?? $this->getFrontendInput();
    }

    public function getClass(array $combineWith = []): string
    {
        return parent::getClass(
            array_unique(
                array_merge(['field', $this->getFrontendInput()], $combineWith)
            )
        );
    }

    public function hasAutocomplete(): bool
    {
        return $this->getAutocomplete() !== '';
    }

    public function getConfig(): EntityFieldConfigInterface
    {
        return $this->config;
    }

    public function isRequired(): bool
    {
        return $this->getData(self::IS_REQUIRED) ?? false;
    }

    public function getAutocomplete(): string
    {
        return $this->getData(self::AUTO_COMPLETE) ?? '';
    }

    public function getDefaultValue()
    {
        return $this->getData(self::DEFAULT_VALUE);
    }

    public function isAutoSave(): bool
    {
        return $this->getData(self::IS_AUTO_SAVE) ?? false;
    }

    public function setOptions(array $options): EntityFieldSelectInterface
    {
        $this->setData(self::OPTIONS, $options);
        return $this;
    }

    public function getOptions(): array
    {
        return $this->getData(self::OPTIONS) ?? [];
    }

    public function hasOptions(): bool
    {
        return count($this->getOptions()) !== 0;
    }

    public function clearOptions(): self
    {
        $this->setData(self::OPTIONS, []);
        return $this;
    }

    public function getValidationRules(): array
    {
        return json_decode($this->getAttributes()['data-validate'] ?? '{}', true);
    }

    public function setValidationRule(string $validationName, $value = true): self
    {
        $rules = $this->getValidationRules();
        $rules[$validationName] = $value;
        $this->setAttribute('data-validate', json_encode($rules));
        return $this;
    }

    public function removeValidationRule(string $validationName): self
    {
        $rules = $this->getValidationRules();
        unset($rules[$validationName]);
        $this->setAttribute('data-validate', json_encode($rules));
        return $this;
    }

    /**
     * @deprecated this method has been deprecated in favor of getLayoutAlias() to eliminate redundant logic.
     *             the getLayoutAlias method, introduced in the abstract element object, extends alias functionality
     *             beyond entity fields, providing a more versatile solution.
     */
    public function getFrontendInputAlias(): string
    {
        return $this->getLayoutAlias();
    }

    /**
     * @deprecated this method has no longer have a solid use case and will
     *             no longer be supported nor should be used by others.
     */
    public function getBlockNameAffix(): string
    {
        return $this->getFrontendInput();
    }
}
