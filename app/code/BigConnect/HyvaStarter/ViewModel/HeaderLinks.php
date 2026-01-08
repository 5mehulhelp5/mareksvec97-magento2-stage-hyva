<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class HeaderLinks implements ArgumentInterface
{
    private const XML_PATH_PREFIX = 'bigconnect_hyvastarter/header_links/';
    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;
    private UrlInterface $urlBuilder;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    public function getItems(): array
    {
        $items = [];

        $mediaBase = rtrim($this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]), '/');
        for ($i = 1; $i <= 4; $i++) {
            $prefix = self::XML_PATH_PREFIX . 'item' . $i . '_';
            if (!$this->scopeConfig->isSetFlag($prefix . 'enabled', ScopeInterface::SCOPE_STORE)) {
                continue;
            }

            $label = trim((string) $this->scopeConfig->getValue($prefix . 'label', ScopeInterface::SCOPE_STORE));
            $url = trim((string) $this->scopeConfig->getValue($prefix . 'url', ScopeInterface::SCOPE_STORE));
            if ($label === '' || $url === '') {
                continue;
            }

            $iconSvg = (string) $this->scopeConfig->getValue($prefix . 'icon_svg', ScopeInterface::SCOPE_STORE);
            $iconAlt = trim((string) $this->scopeConfig->getValue($prefix . 'icon_alt', ScopeInterface::SCOPE_STORE));

            $items[] = [
                'label' => $label,
                'url' => $url,
                'variant' => $this->normalizeVariant((string) $this->scopeConfig->getValue($prefix . 'variant', ScopeInterface::SCOPE_STORE)),
                'icon_svg_url' => $this->buildIconUrl($iconSvg, $mediaBase),
                'icon_alt' => $iconAlt !== '' ? $iconAlt : $label,
                'icon_blink' => $this->scopeConfig->isSetFlag($prefix . 'icon_blink', ScopeInterface::SCOPE_STORE),
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
        return strtolower(trim($variant)) === 'cta' ? 'cta' : 'link';
    }

    private function buildIconUrl(string $value, string $mediaBase): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        $value = ltrim($value, '/');
        if (stripos($value, 'media/') === 0) {
            $value = substr($value, 6);
        }
        $prefix = 'bigconnect/hyvastarter/icons/';
        if (stripos($value, $prefix) !== 0) {
            $value = $prefix . $value;
        }

        return $mediaBase . '/' . $value;
    }
}
