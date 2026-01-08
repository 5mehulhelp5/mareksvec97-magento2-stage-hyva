<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class FooterConfig implements ArgumentInterface
{
    private const XML_PATH_PREFIX = 'hyvastarter/footer/';

    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getAboutText(): string
    {
        return (string) $this->getValue('about_text');
    }

    public function getFacebookUrl(): string
    {
        return (string) $this->getValue('facebook_url');
    }

    public function getLogoUrl(): string
    {
        $logo = ltrim((string) $this->getValue('logo'), '/');
        if ($logo === '') {
            return '';
        }

        $baseUrl = rtrim(
            $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA),
            '/'
        );

        return $baseUrl . '/hyvastarter/footer/' . $logo;
    }

    public function getInstagramUrl(): string
    {
        return (string) $this->getValue('instagram_url');
    }

    public function getYoutubeUrl(): string
    {
        return (string) $this->getValue('youtube_url');
    }

    public function getCol2Title(): string
    {
        return (string) $this->getValue('col2_title');
    }

    public function getCol2Html(): string
    {
        return (string) $this->getValue('col2_html');
    }

    public function getCol3Title(): string
    {
        return (string) $this->getValue('col3_title');
    }

    public function getCol3Html(): string
    {
        return (string) $this->getValue('col3_html');
    }

    public function getCol4Title(): string
    {
        return (string) $this->getValue('col4_title');
    }

    public function getCol4Html(): string
    {
        return (string) $this->getValue('col4_html');
    }

    public function getShowPhone(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'show_phone',
            ScopeInterface::SCOPE_STORE
        );
    }

    private function getValue(string $field): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . $field,
            ScopeInterface::SCOPE_STORE
        );
    }
}
