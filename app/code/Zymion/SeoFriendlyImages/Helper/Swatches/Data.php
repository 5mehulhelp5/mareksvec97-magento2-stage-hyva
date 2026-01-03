<?php

namespace Zymion\SeoFriendlyImages\Helper\Swatches;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory as SwatchCollectionFactory;
use Magento\Swatches\Model\SwatchAttributesProvider;
use Magento\Swatches\Model\SwatchAttributeType;

class Data extends \Magento\Swatches\Helper\Data
{
    /**
     * @var ModelProduct
     */
    private $product;

    /**
     * @var UrlBuilder
     */
    private $imageUrlBuilder;

    /**
     * @var \Zymion\SeoFriendlyImages\Helper\Data
     */
    private $seoFriendlyImagesHelper;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param SwatchCollectionFactory $swatchCollectionFactory
     * @param UrlBuilder $urlBuilder
     * @param \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper
     * @param Json|null $serializer
     * @param SwatchAttributesProvider $swatchAttributesProvider
     * @param SwatchAttributeType|null $swatchTypeChecker
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        SwatchCollectionFactory $swatchCollectionFactory,
        UrlBuilder $urlBuilder,
        \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper,
        Json $serializer = null,
        SwatchAttributesProvider $swatchAttributesProvider = null,
        SwatchAttributeType $swatchTypeChecker = null
    ) {
        parent::__construct(
            $productCollectionFactory,
            $productRepository,
            $storeManager,
            $swatchCollectionFactory,
            $urlBuilder,
            $serializer,
            $swatchAttributesProvider,
            $swatchTypeChecker
        );
        
        $this->imageUrlBuilder = $urlBuilder;
        $this->seoFriendlyImagesHelper = $seoFriendlyImagesHelper;
    }

    /**
     * Method getting full media gallery for current Product
     *
     * @param ModelProduct $product
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductMediaGallery(ModelProduct $product): array
    {
        $this->product = $product;

        $baseImage = null;
        $gallery = [];

        $mediaGallery = $product->getMediaGalleryEntries();
        /** @var ProductAttributeMediaGalleryEntryInterface $mediaEntry */
        foreach ($mediaGallery as $mediaEntry) {
            if ($mediaEntry->isDisabled()) {
                continue;
            }
            if (!$baseImage || $this->isMainImage($mediaEntry)) {
                $baseImage = $mediaEntry;
            }

            $gallery[$mediaEntry->getId()] = $this->collectImageData($mediaEntry);
        }

        if (!$baseImage) {
            return [];
        }

        $resultGallery = $this->collectImageData($baseImage);
        $resultGallery['gallery'] = $gallery;

        return $resultGallery;
    }

    /**
     * Checks if image is main image in gallery
     *
     * @param ProductAttributeMediaGalleryEntryInterface $mediaEntry
     * @return bool
     */
    private function isMainImage(ProductAttributeMediaGalleryEntryInterface $mediaEntry): bool
    {
        return in_array('image', $mediaEntry->getTypes(), true);
    }

    /**
     * Returns image data for swatches
     *
     * @param ProductAttributeMediaGalleryEntryInterface $mediaEntry
     * @return array
     */
    private function collectImageData(ProductAttributeMediaGalleryEntryInterface $mediaEntry): array
    {
        $image = $this->getAllSizeImages($mediaEntry->getFile());
        $image[ProductAttributeMediaGalleryEntryInterface::POSITION] =  $mediaEntry->getPosition();
        $image['isMain'] = $this->isMainImage($mediaEntry);
        return $image;
    }

    /**
     * Get all size images
     *
     * @param string $imageFile
     * @return array
     */
    private function getAllSizeImages($imageFile)
    {
        $newFile = $this->seoFriendlyImagesHelper->getSeoFriendlyImageName($this->product, $imageFile);

        return [
            'large' => $this->imageUrlBuilder->getUrl($newFile, 'product_swatch_image_large'),
            'medium' => $this->imageUrlBuilder->getUrl($newFile, 'product_swatch_image_medium'),
            'small' => $this->imageUrlBuilder->getUrl($newFile, 'product_swatch_image_small')
        ];
    }
}
