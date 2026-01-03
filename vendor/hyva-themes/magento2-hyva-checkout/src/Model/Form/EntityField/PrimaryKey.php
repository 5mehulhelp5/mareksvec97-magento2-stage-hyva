<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

class PrimaryKey extends AbstractEntityField
{
    public function getId(): string
    {
        return 'id';
    }

    public function getFrontendInput(): string
    {
        return 'hidden';
    }

    public function getWrapperClasses(array $combineWith = []): array
    {
        return [];
    }

    public function getName(): string
    {
        return 'id';
    }

    public function getLabel(): string
    {
        return 'Primary key';
    }

    public function getAutocomplete(): string
    {
        return '';
    }

    public function getDefaultValue()
    {
        return null;
    }

    public function isAutoSave(): bool
    {
        return false;
    }

    public function getSortOrder(): int
    {
        return 0;
    }

    public function isRequired(): bool
    {
        return false;
    }
}
