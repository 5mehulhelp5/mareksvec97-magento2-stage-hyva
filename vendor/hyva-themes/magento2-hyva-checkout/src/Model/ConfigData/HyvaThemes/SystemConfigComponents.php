<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigComponents
{
    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve component config value by path and scope.
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $group
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|int|string $scopeCode
     * @return mixed
     */
    public function getComponentValue(
        string $path,
        string $group,
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        $path = sprintf('hyva_themes_checkout/component/%s/%s', $group, trim($path));
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Retrieve component config flag by path and scope.
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $group
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isSetFlagForComponent(
        string $path,
        string $group,
        string $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool {
        $path = sprintf('hyva_themes_checkout/component/%s/%s', $group, trim($path));
        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }
}
