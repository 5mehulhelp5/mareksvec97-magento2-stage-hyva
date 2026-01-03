<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigComponents;

class SystemConfigCustomerComment
{
    protected SystemConfigComponents $systemConfigComponents;

    public function __construct(
        SystemConfigComponents $systemConfigComponents
    ) {
        $this->systemConfigComponents = $systemConfigComponents;
    }

    public function getPlaceholderText(): string
    {
        return $this->systemConfigComponents->getComponentValue('placeholder', 'order_comment') ?? '';
    }
}
