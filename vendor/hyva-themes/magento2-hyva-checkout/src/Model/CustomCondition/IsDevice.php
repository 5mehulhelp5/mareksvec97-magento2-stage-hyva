<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\CustomCondition;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigDeveloper;
use Hyva\Checkout\Model\CustomConditionInterface;
use Magento\Framework\HTTP\Header as HttpHeader;

class IsDevice implements CustomConditionInterface
{
    protected HttpHeader $httpHeader;
    protected SystemConfigDeveloper $systemConfigDeveloper;

    public function __construct(
        HttpHeader $httpHeader,
        SystemConfigDeveloper $systemConfigDeveloper
    ) {
        $this->httpHeader = $httpHeader;
        $this->systemConfigDeveloper = $systemConfigDeveloper;
    }

    public function validate(): bool
    {
        return true;
    }

    public function onMobile(): bool
    {
        $mobileUserAgentRegex = $this->systemConfigDeveloper->getMobileUserAgentRegex();

        // This is not on a mobile-first approach. So when empty, the device will be seen as not-mobile.
        if (empty($mobileUserAgentRegex)) {
            return false;
        }

        return (bool) preg_match($mobileUserAgentRegex, $this->httpHeader->getHttpUserAgent());
    }

    public function onDesktop(): bool
    {
        return ! $this->onMobile();
    }
}
