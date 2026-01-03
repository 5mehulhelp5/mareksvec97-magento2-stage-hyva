<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField\EavEntityAddress;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Hyva\Checkout\Model\Form\EntityField\FormFieldDependencies;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\ObjectManager;

class CountryAttributeField extends EavAttributeField
{
    protected DirectoryHelper $directoryHelper;

    public function __construct(
        FormFieldDependencies $context,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->directoryHelper = $directoryHelper
            ?: ObjectManager::getInstance()->get(DirectoryHelper::class);
        parent::__construct($context);
    }

    public function getDefaultValue(): ?string
    {
        $options = $this->getOptions();
        $value = $this->scopeConfig->getValue('general/country/default', ScopeInterface::SCOPE_STORE);

        if (empty($options)) {
            return null;
        }

        // Let's make sure the default country ID is available between the available countries.
        $search = in_array($value, array_column($options, 'value'));

        if ($search) {
            return $value;
        }

        // Return the first available country in the options list.
        $value = reset($options);
        return $value['value'] ?? null;
    }

    public function getOptions(): array
    {
        $value = $this->getValue();
        $topCountries = $this->directoryHelper->getTopCountryCodes();

        $options = array_map(static function (Option $option) {
            return $option->toArray();
        }, array_filter($this->attribute->getOptions(), static function (Option $option) {
            return ! empty((string) $option->getValue()) || ! empty(trim((string) $option->getLabel()));
        }));

        // Move top countries to the top of the options array
        if (! empty($topCountries)) {
            $topOptions = [];

            $options = array_filter($options, function ($option) use ($topCountries, &$topOptions) {
                if (in_array($option['value'], $topCountries)) {
                    $topOptions[] = $option;
                    return false;
                }
                return true;
            });

            if (! empty($topOptions)) {
                $options = array_merge($topOptions, $options);
            }
        }

        if (! in_array($value, array_column($options, 'value'))) {
            array_unshift($options, ['value' => '', 'label' => 'Please select an option.']);
        }

        return $options;
    }

    public function isAutoSave(): bool
    {
        return false;
    }
}
