<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigCustomerComment;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomerComment implements ArgumentInterface
{
    private SystemConfigCustomerComment $systemConfigCustomerComment;

    public function __construct(
        SystemConfigCustomerComment $systemConfigCustomerComment
    ) {
        $this->systemConfigCustomerComment = $systemConfigCustomerComment;
    }

    public function hasPlaceholderText(): bool
    {
        return ! empty($this->getPlaceholderText());
    }

    public function getPlaceholderText(): string
    {
        return $this->systemConfigCustomerComment->getPlaceholderText();
    }
}
