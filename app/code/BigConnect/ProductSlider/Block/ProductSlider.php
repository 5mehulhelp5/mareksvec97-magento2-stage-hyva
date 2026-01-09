<?php

declare(strict_types=1);

namespace BigConnect\ProductSlider\Block;

use BigConnect\ProductSlider\ViewModel\ProductSliderViewModel;
use Hyva\Theme\Model\ViewModelRegistry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class ProductSlider extends Template
{
    protected $_template = 'BigConnect_ProductSlider::widget/product-slider.phtml';

    private ProductSliderViewModel $viewModel;
    private ?ViewModelRegistry $viewModelRegistry;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        Template\Context $context,
        ProductSliderViewModel $viewModel,
        ScopeConfigInterface $scopeConfig,
        ?ViewModelRegistry $viewModelRegistry = null,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewModel = $viewModel;
        $this->scopeConfig = $scopeConfig;
        $this->viewModelRegistry = $viewModelRegistry;
    }

    public function getViewModel(): ProductSliderViewModel
    {
        return $this->viewModel;
    }

    /**
     * Hyvä ViewModel registry (na heroicons a iné Hyvä viewmodely)
     */
    public function getViewModelRegistry(): ?ViewModelRegistry
    {
        return $this->viewModelRegistry;
    }

    public function getSourceCode(): string
    {
        return (string) ($this->getData('source_code') ?: 'bestsellers');
    }

    public function getTitleBadge(): string
    {
        return (string) ($this->getData('title_badge') ?: 'Najpredávanejšie produkty');
    }

    public function getPreset(): string
    {
        return (string) ($this->getData('preset') ?: 'item1');
    }

    public function isPresetEnabled(): bool
    {
        $preset = $this->getPreset();
        if ($preset === '') {
            return false;
        }

        return $this->scopeConfig->isSetFlag(
            $this->getPresetConfigPath($preset, 'enabled'),
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTitle(): string
    {
        return $this->getConfigValue('title', '');
    }

    public function getSubtitle(): string
    {
        return $this->getConfigValue('subtitle', '');
    }

    public function getPageSize(): int
    {
        return (int) ($this->getData('page_size') ?: 8);
    }

    public function getPeriodDays(): int
    {
        return (int) ($this->getData('period_days') ?: 30);
    }

    public function isOnlyInStock(): bool
    {
        return (bool) ($this->getData('only_in_stock') ?? true);
    }

    public function isOnlyVisible(): bool
    {
        return (bool) ($this->getData('only_visible') ?? true);
    }

    public function isAddToCartEnabled(): bool
    {
        return (bool) ($this->getData('add_to_cart') ?? true);
    }

    public function isWishlistEnabled(): bool
    {
        return (bool) ($this->getData('show_wishlist') ?? false);
    }

    public function getViewAllUrl(): string
    {
        return $this->getConfigValue('view_all_url', '');
    }

    public function getViewAllLabel(): string
    {
        return $this->getConfigValue('view_all_label', '');
    }

    public function getAccentColor(): string
    {
        return $this->getConfigValue('accent_color', '#7e9b84');
    }

    private function getConfigValue(string $field, string $default): string
    {
        if ($this->hasData($field)) {
            return (string) $this->getData($field);
        }

        $preset = $this->getPreset();
        if ($preset === '') {
            return $default;
        }

        $value = $this->scopeConfig->getValue(
            $this->getPresetConfigPath($preset, $field),
            ScopeInterface::SCOPE_STORE
        );

        if ($value === null || $value === '') {
            return $default;
        }

        return (string) $value;
    }

    private function getPresetConfigPath(string $preset, string $field): string
    {
        return sprintf('hyvastarter/product_slider/%s/%s', $preset, $field);
    }
}
