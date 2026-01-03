<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

use Hyva\Checkout\Model\Form\EntityFieldConfigInterface;

interface EavAttributeMappingConfigInterface extends EntityFieldConfigInterface
{
    public const ATTRIBUTE_CODE = 'attribute_code';
    public const ATTRIBUTE_CODE_ALIAS = 'attribute_code_alias';
    public const SORT_ORDER = 'sort_order';
    public const ENABLED = 'enabled';
    public const REQUIRED = 'required';
    public const AUTO_SAVE = 'auto_save';
    public const LENGTH = 'length';
    public const TOOL_TIP = 'tool_tip';
    public const COMMENT = 'comment';

    public function getAttributeCode(): ?string;

    public function getAttributeCodeAlias(): ?string;

    public function isEnabled(): bool;

    public function getSortOrder(): int;

    public function getRequired(): bool;

    public function getLength(): int;

    public function getTooltip(): string;

    public function getComment(): string;
}
