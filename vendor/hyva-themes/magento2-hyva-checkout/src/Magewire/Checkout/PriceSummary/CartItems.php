<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\PriceSummary;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Checkout as SystemCheckoutConfig;
use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Helper\Product\ConfigurationPool as ProductConfigurationPoolHelper;
use Magento\Checkout\Model\Cart\ImageProvider as ProductImageProvider;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magewirephp\Magewire\Component;

class CartItems extends Component
{
    public bool $expand = false;

    protected array $uncallables = ['getQuoteItemData'];

    protected SystemCheckoutConfig $systemCheckoutConfig;
    protected ProductImageProvider $productImageProvider;
    protected SessionCheckout $sessionCheckout;
    protected CartItemRepositoryInterface $quoteItemRepository;
    protected CatalogImageHelper $catalogImageHelper;
    protected ProductConfigurationPoolHelper $productConfigurationPoolHelper;

    public function __construct(
        SystemCheckoutConfig $systemCheckoutConfig,
        ProductImageProvider $productImageProvider,
        SessionCheckout $sessionCheckout,
        CartItemRepositoryInterface $quoteItemRepository,
        CatalogImageHelper $catalogImageHelper,
        ProductConfigurationPoolHelper $productConfigurationPoolHelper
    ) {
        $this->systemCheckoutConfig = $systemCheckoutConfig;
        $this->productImageProvider = $productImageProvider;
        $this->sessionCheckout = $sessionCheckout;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->catalogImageHelper = $catalogImageHelper;
        $this->productConfigurationPoolHelper = $productConfigurationPoolHelper;
    }

    public function mount(): void
    {
        $quote = $this->sessionCheckout->getQuote();

        $this->expand = $this->systemCheckoutConfig->canCartItemsUnfold((int)$quote->getItemsCount());
    }

    public function getQuoteItemData(): ?array
    {
        $quoteItems = [];

        try {
            $quoteEntity = $this->sessionCheckout->getQuote()->getId();
            $quoteItemList = $this->quoteItemRepository->getList($quoteEntity);

            foreach ($quoteItemList as $index => $quoteItem) {
                $thumbnail = $this->catalogImageHelper->init($quoteItem->getProduct(), 'product_thumbnail_image');
                $quoteItems[$index] = $quoteItem->toArray();

                $quoteItems[$index]['options'] = $this->getFormattedOptionValue($quoteItem);
                $quoteItems[$index]['thumbnail'] = $thumbnail->getUrl();
                $quoteItems[$index]['message'] = $quoteItem->getMessage();
            }
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return null;
        }

        return $quoteItems;
    }

    /**
     * Retrieve formatted item options view
     */
    protected function getFormattedOptionValue(CartItemInterface $item): array
    {
        $optionsData = [];
        $options = $this->productConfigurationPoolHelper->getByProductType($item->getProductType())->getOptions($item);

        foreach ($options as $index => $optionValue) {
            /* @var $helper Configuration */
            $helper = $this->productConfigurationPoolHelper->getByProductType('default');

            $option = $helper->getFormattedOptionValue($optionValue);
            $optionsData[$index] = $option;
            $optionsData[$index]['label'] = $optionValue['label'];
        }

        return $optionsData;
    }
}
