<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class AboutSection implements ArgumentInterface
{
    private const XML_PATH_PREFIX = 'hyvastarter/about_section/';

    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTitle(): string
    {
        return $this->getValue('title');
    }

    public function getText(): string
    {
        return $this->getValue('text');
    }

    public function getFeatures(): array
    {
        $features = [];
        for ($i = 1; $i <= 3; $i++) {
            $value = trim($this->getValue('feature' . $i));
            if ($value !== '') {
                $features[] = $value;
            }
        }

        return $features;
    }

    public function getStats(): array
    {
        $stats = [];
        for ($i = 1; $i <= 3; $i++) {
            $number = trim($this->getValue('stat' . $i . '_number'));
            $label = trim($this->getValue('stat' . $i . '_label'));
            if ($number === '' || $label === '') {
                continue;
            }
            $stats[] = [
                'number' => $number,
                'label' => $label,
            ];
        }

        return $stats;
    }

    public function getCtaLabel(): string
    {
        return $this->getValue('cta_label');
    }

    public function getCtaUrl(): string
    {
        return $this->getValue('cta_url');
    }

    public function getImageUrl(): string
    {
        $value = ltrim($this->getValue('image'), '/');
        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        $mediaBase = rtrim(
            $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
            '/'
        );

        return $mediaBase . '/hyvastarter/about/' . $value;
    }

    public function getBadgeTitle(): string
    {
        return $this->getValue('badge_title');
    }

    public function getBadgeText(): string
    {
        return $this->getValue('badge_text');
    }

    private function getValue(string $field): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . $field,
            ScopeInterface::SCOPE_STORE
        );
    }
}
