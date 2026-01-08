<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class HeaderLinks implements ArgumentInterface
{
    private const XML_PATH_PREFIX = 'bigconnect_hyvastarter/header_links/';
    private const ICONS = [
        'sparkles',
        'factory',
        'info',
        'phone',
        'mail',
        'map',
        'message',
        'star',
    ];

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getItems(): array
    {
        $items = [];

        for ($i = 1; $i <= 10; $i++) {
            $prefix = self::XML_PATH_PREFIX . 'item' . $i . '/';
            if (!$this->scopeConfig->isSetFlag($prefix . 'enabled', ScopeInterface::SCOPE_STORE)) {
                continue;
            }

            $label = trim((string) $this->scopeConfig->getValue($prefix . 'label', ScopeInterface::SCOPE_STORE));
            $url = trim((string) $this->scopeConfig->getValue($prefix . 'url', ScopeInterface::SCOPE_STORE));
            if ($label === '' || $url === '') {
                continue;
            }

            $items[] = [
                'label' => $label,
                'url' => $url,
                'icon' => $this->normalizeIcon((string) $this->scopeConfig->getValue($prefix . 'icon', ScopeInterface::SCOPE_STORE)),
                'variant' => $this->normalizeVariant((string) $this->scopeConfig->getValue($prefix . 'variant', ScopeInterface::SCOPE_STORE)),
                'sort_order' => (int) $this->scopeConfig->getValue($prefix . 'sort_order', ScopeInterface::SCOPE_STORE),
                'target_blank' => $this->scopeConfig->isSetFlag($prefix . 'target_blank', ScopeInterface::SCOPE_STORE),
            ];
        }

        usort($items, static function (array $a, array $b): int {
            return $a['sort_order'] <=> $b['sort_order'];
        });

        return array_map(static function (array $item): array {
            unset($item['sort_order']);
            return $item;
        }, $items);
    }

    public function normalizeVariant(string $variant): string
    {
        return strtolower(trim($variant)) === 'cta' ? 'cta' : 'default';
    }

    public function normalizeIcon(string $icon): string
    {
        $icon = strtolower(trim($icon));
        return in_array($icon, self::ICONS, true) ? $icon : 'info';
    }
}
