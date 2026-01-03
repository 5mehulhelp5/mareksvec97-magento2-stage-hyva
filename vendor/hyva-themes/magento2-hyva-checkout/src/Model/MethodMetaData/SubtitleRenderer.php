<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\MethodMetaData;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SubtitleRenderer
{
    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function render(string $subtitle): string
    {
        if ($this->isConfigPath($subtitle)) {
            $subtitle = $this->getSubtitleByConfigPath($subtitle);
        }

        return ucfirst($subtitle);
    }

    protected function isConfigPath(string $path): bool
    {
        return is_string($this->getSubtitleByConfigPath($path));
    }

    protected function getSubtitleByConfigPath(string $path): ?string
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}
