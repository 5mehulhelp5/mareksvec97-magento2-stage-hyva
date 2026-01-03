<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField\Config;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeMappingConfigInterface;
use Hyva\Checkout\Model\Form\EntityField\EntityFieldConfig;

class EavAttributeMappingConfig extends EntityFieldConfig implements EavAttributeMappingConfigInterface
{
    public function getAttributeCode(): ?string
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    public function getAttributeCodeAlias(): ?string
    {
        return $this->getData(self::ATTRIBUTE_CODE_ALIAS);
    }

    public function isEnabled(): bool
    {
        return $this->getData(self::ENABLED) ?? true;
    }

    public function getSortOrder(): int
    {
        return (int) $this->getData(self::SORT_ORDER) ?? 0;
    }

    public function getRequired(): bool
    {
        return (bool) $this->getData(self::REQUIRED) ?? false;
    }

    public function getAutoSave(): bool
    {
        return (bool) $this->getData(self::AUTO_SAVE) ?? false;
    }

    public function getLength(): int
    {
        return (int) $this->getData(self::LENGTH) ?? 1;
    }

    public function getTooltip(): string
    {
        return $this->getData(self::TOOL_TIP) ?? '';
    }

    public function getComment(): string
    {
        return $this->getData(self::COMMENT) ?? '';
    }
}
