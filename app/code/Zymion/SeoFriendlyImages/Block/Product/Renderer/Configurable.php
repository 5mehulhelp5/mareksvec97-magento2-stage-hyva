<?php

namespace Zymion\SeoFriendlyImages\Block\Product\Renderer;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product as CatalogProduct;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\Swatch;
use Magento\Framework\App\ObjectManager;
use Magento\Swatches\Model\SwatchAttributesProvider;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    /**
     * @var UrlBuilder
     */
    private $imageUrlBuilder;

    /**
     * @var \Zymion\SeoFriendlyImages\Helper\Data
     */
    private $seoFriendlyImagesHelper;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param EncoderInterface $jsonEncoder
     * @param Data $helper
     * @param CatalogProduct $catalogProduct
     * @param CurrentCustomer $currentCustomer
     * @param PriceCurrencyInterface $priceCurrency
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param SwatchData $swatchHelper
     * @param Media $swatchMediaHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper
     * @param array $data
     * @param SwatchAttributesProvider|null $swatchAttributesProvider
     * @param UrlBuilder|null $imageUrlBuilder
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        Data $helper,
        CatalogProduct $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        SwatchData $swatchHelper,
        Media $swatchMediaHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper,
        array $data = [],
        SwatchAttributesProvider $swatchAttributesProvider = null,
        UrlBuilder $imageUrlBuilder = null
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $swatchHelper,
            $swatchMediaHelper,
            $data,
            $swatchAttributesProvider,
            $imageUrlBuilder
        );

        $this->seoFriendlyImagesHelper = $seoFriendlyImagesHelper;
        $this->imageUrlBuilder = $imageUrlBuilder ?? ObjectManager::getInstance()->get(UrlBuilder::class);
    }

    /**
     * Get swatch product image.
     *
     * @param Product $childProduct
     * @param string $imageType
     * @return string
     */
    protected function getSwatchProductImage(Product $childProduct, $imageType)
    {
        if ($this->isProductHasImage($childProduct, Swatch::SWATCH_IMAGE_NAME)) {
            $swatchImageId = $imageType;
            $imageAttributes = ['type' => Swatch::SWATCH_IMAGE_NAME];
        } elseif ($this->isProductHasImage($childProduct, 'image')) {
            $swatchImageId = $imageType == Swatch::SWATCH_IMAGE_NAME ? 'swatch_image_base' : 'swatch_thumb_base';
            $imageAttributes = ['type' => 'image'];
        }

        if (!empty($swatchImageId) && !empty($imageAttributes['type'])) {
            $originalFilePath = $childProduct->getData($imageAttributes['type']);
            $newFile = $this->seoFriendlyImagesHelper->getSeoFriendlyImageName($childProduct, $originalFilePath);

            return $this->imageUrlBuilder->getUrl($newFile, $swatchImageId);
        }
    }
}
