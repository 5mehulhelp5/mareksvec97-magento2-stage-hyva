<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

class SaveAs extends AbstractEntityField
{
    public function getFrontendInput(): string
    {
        return 'checkbox';
    }

    public function getName(): string
    {
        return 'save';
    }

    public function getLabel(): string
    {
        $label = parent::getLabel();

        return strlen($label) === 0 ? 'Save' : $label;
    }

    public function getAutocomplete(): string
    {
        return '';
    }

    public function getDefaultValue(): bool
    {
        return parent::getDefaultValue() ?? false;
    }

    public function isAutoSave(): bool
    {
        return parent::isAutoSave() ?? true;
    }

    public function isRequired(): bool
    {
        return parent::isRequired() ?? false;
    }
}
