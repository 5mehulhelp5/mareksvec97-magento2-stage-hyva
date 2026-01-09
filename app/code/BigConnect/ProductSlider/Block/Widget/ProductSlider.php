<?php

declare(strict_types=1);

namespace BigConnect\ProductSlider\Block\Widget;

use BigConnect\ProductSlider\ViewModel\ProductSliderViewModel;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ProductSlider extends Template implements BlockInterface
{
    protected $_template = 'BigConnect_ProductSlider::widget/product-slider.phtml';

    private ProductSliderViewModel $viewModel;

    public function __construct(
        Template\Context $context,
        ProductSliderViewModel $viewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewModel = $viewModel;
    }

    public function getViewModel(): ProductSliderViewModel
    {
        return $this->viewModel;
    }

    public function getSourceCode(): string
    {
        return (string)($this->getData('source_code') ?: 'bestsellers');
    }

    public function getTitleBadge(): string
    {
        return (string)($this->getData('title_badge') ?: 'Najpredávanejšie produkty');
    }

    public function getTitle(): string
    {
        return (string)($this->getData('title') ?: 'Obľúbené u našich zákazníkov');
    }

    public function getSubtitle(): string
    {
        return (string)($this->getData('subtitle') ?: 'Produkty s najvyšším hodnotením a tisíckami spokojných zákazníkov');
    }

    public function getPageSize(): int
    {
        return (int)($this->getData('page_size') ?: 8);
    }

    public function getPeriodDays(): int
    {
        return (int)($this->getData('period_days') ?: 30);
    }

    public function isOnlyInStock(): bool
    {
        return (bool)($this->getData('only_in_stock') ?? true);
    }

    public function isOnlyVisible(): bool
    {
        return (bool)($this->getData('only_visible') ?? true);
    }

    public function isAddToCartEnabled(): bool
    {
        return (bool)($this->getData('add_to_cart') ?? true);
    }

    public function isWishlistEnabled(): bool
    {
        return (bool)($this->getData('show_wishlist') ?? false);
    }

    public function getViewAllUrl(): string
    {
        return (string)($this->getData('view_all_url') ?? '');
    }

    public function getViewAllLabel(): string
    {
        return (string)($this->getData('view_all_label') ?: 'Zobraziť všetky produkty');
    }

    public function getAccentColor(): string
    {
        return (string)($this->getData('accent_color') ?: '#7e9b84');
    }
}
