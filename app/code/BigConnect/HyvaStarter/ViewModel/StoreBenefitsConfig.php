<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class StoreBenefitsConfig implements ArgumentInterface
{
    private const XML_PATH_PREFIX = 'hyvastarter/store_benefits/';

    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getItems(): array
    {
        $items = [];
        $iconMap = $this->getIconMap();

        for ($i = 1; $i <= 4; $i++) {
            $title = $this->getValue("b{$i}_title");
            $text = $this->getValue("b{$i}_text");
            $icon = strtolower(trim($this->getValue("b{$i}_icon")));
            $customIcon = $this->getValue("b{$i}_icon_custom");

            if (trim($title) === '' && trim($text) === '') {
                continue;
            }

            $items[] = [
                'title' => $title,
                'text' => $text,
                'icon_method' => $iconMap[$icon] ?? $iconMap['fallback'],
                'icon_custom_url' => $this->getCustomIconUrl($customIcon),
            ];
        }

        return $items;
    }

    private function getValue(string $field): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . $field,
            ScopeInterface::SCOPE_STORE
        );
    }

    private function getCustomIconUrl(string $value): string
    {
        $value = ltrim($value, '/');
        if ($value === '') {
            return '';
        }

        $baseUrl = rtrim(
            $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA),
            '/'
        );

        return $baseUrl . '/hyvastarter/benefits/' . $value;
    }

    private function getIconMap(): array
    {
        return [
            'shield' => 'shieldCheckHtml',
            'globe' => 'globeAltHtml',
            'badge' => 'badgeCheckHtml',
            'thumb' => 'thumbUpHtml',
            'phone' => 'phoneHtml',
            'fallback' => 'badgeCheckHtml',
        ];
    }
}
