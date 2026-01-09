<?php

declare(strict_types=1);

namespace BigConnect\ProductSlider\ViewModel;

use BigConnect\ProductSlider\Model\Source\SourcePool;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

class ProductSliderViewModel implements ArgumentInterface
{
    private SourcePool $sourcePool;
    private PriceHelper $priceHelper;
    private ImageHelper $imageHelper;
    private CartHelper $cartHelper;
    private UrlHelper $urlHelper;
    private ?WishlistHelper $wishlistHelper;

    public function __construct(
        SourcePool $sourcePool,
        PriceHelper $priceHelper,
        ImageHelper $imageHelper,
        CartHelper $cartHelper,
        UrlHelper $urlHelper,
        ?WishlistHelper $wishlistHelper = null
    ) {
        $this->sourcePool = $sourcePool;
        $this->priceHelper = $priceHelper;
        $this->imageHelper = $imageHelper;
        $this->cartHelper = $cartHelper;
        $this->urlHelper = $urlHelper;
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * @param array $config
     * @return ProductInterface[]
     */
    public function getItems(string $sourceCode, array $config): array
    {
        if (!$this->sourcePool->has($sourceCode)) {
            return [];
        }

        return $this->sourcePool->get($sourceCode)->getItems($config);
    }

    public function getSourceLabel(string $sourceCode): string
    {
        if (!$this->sourcePool->has($sourceCode)) {
            return '';
        }

        return $this->sourcePool->get($sourceCode)->getLabel();
    }

    public function getFormattedPrice(ProductInterface $product): string
    {
        return $this->priceHelper->currency((float) $product->getFinalPrice(), true, false);
    }

    public function getFormattedOriginalPrice(ProductInterface $product): string
    {
        return $this->priceHelper->currency((float) $product->getPrice(), true, false);
    }

    public function hasDiscount(ProductInterface $product): bool
    {
        return (float) $product->getFinalPrice() < (float) $product->getPrice();
    }

    public function getDiscountPercent(ProductInterface $product): string
    {
        $price = (float) $product->getPrice();
        $final = (float) $product->getFinalPrice();

        if ($price <= 0 || $final >= $price) {
            return '';
        }

        $percent = (int) round((($price - $final) / $price) * 100);

        return sprintf('-%d%%', $percent);
    }

    public function getImageUrl(ProductInterface $product, string $imageId = 'product_base_image'): string
    {
        return $this->imageHelper->init($product, $imageId)->getUrl();
    }

    /**
     * Hyvä / Magento štýl: template očakáva array s kľúčmi 'action' a 'data'
     */
    public function getAddToCartPostParams(ProductInterface $product): array
    {
        $url = $this->cartHelper->getAddUrl($product);

        return [
            'action' => $url,
            'data' => [
                'product' => (int) $product->getId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ],
        ];
    }

    public function getWishlistPostParams(ProductInterface $product): ?string
    {
        if ($this->wishlistHelper === null) {
            return null;
        }

        return $this->wishlistHelper->getAddParams($product);
    }
}
