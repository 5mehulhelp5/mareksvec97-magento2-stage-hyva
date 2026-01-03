<?php

namespace Zymion\SeoFriendlyImages\Model\ResourceModel\Catalog;

use Magento\Framework\App\ObjectManager;

class Product extends \Magento\Sitemap\Model\ResourceModel\Catalog\Product
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $catalogImageHelper;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Sitemap\Helper\Data $sitemapData
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\ResourceModel\Product\Gallery $mediaGalleryResourceModel
     * @param \Magento\Catalog\Model\Product\Gallery\ReadHandler $mediaGalleryReadHandler
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param string $connectionName
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Catalog\Helper\Image $catalogImageHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $mediaGalleryResourceModel,
        \Magento\Catalog\Model\Product\Gallery\ReadHandler $mediaGalleryReadHandler,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        $connectionName = null,
        \Magento\Catalog\Model\Product $productModel = null,
        \Magento\Catalog\Helper\Image $catalogImageHelper = null
    ) {
        $this->catalogImageHelper = $catalogImageHelper
            ?: ObjectManager::getInstance()->get(\Magento\Catalog\Helper\Image::class);
        parent::__construct(
            $context,
            $sitemapData,
            $productResource,
            $storeManager,
            $productVisibility,
            $productStatus,
            $mediaGalleryResourceModel,
            $mediaGalleryReadHandler,
            $mediaConfig,
            $connectionName,
            $productModel,
            $catalogImageHelper
        );
    }

    /**
     * Load product images
     *
     * @param \Magento\Framework\DataObject $product
     * @param int $storeId
     * @return void
     */
    protected function _loadProductImages($product, $storeId)
    {
        /** @var $helper \Magento\Sitemap\Helper\Data */
        $helper = $this->_sitemapData;
        $imageIncludePolicy = $helper->getProductImageIncludePolicy($storeId);

        // Get product images
        $imagesCollection = [];
        if (\Magento\Sitemap\Model\Source\Product\Image\IncludeImage::INCLUDE_ALL == $imageIncludePolicy) {
            $imagesCollection = $this->_getAllProductImages($product, $storeId);
        } elseif (\Magento\Sitemap\Model\Source\Product\Image\IncludeImage::INCLUDE_BASE == $imageIncludePolicy &&
            $product->getImage() &&
            $product->getImage() != self::NOT_SELECTED_IMAGE
        ) {
            $imagesCollection = [
                new \Magento\Framework\DataObject(
                    ['url' => $this->getProductImageUrl($product->getImage(), $product)]
                ),
            ];
        }

        if ($imagesCollection) {
            // Determine thumbnail path
            $thumbnail = $product->getThumbnail();
            if ($thumbnail && $product->getThumbnail() != self::NOT_SELECTED_IMAGE) {
                $thumbnail = $this->getProductImageUrl($thumbnail, $product);
            } else {
                $thumbnail = $imagesCollection[0]->getUrl();
            }

            $product->setImages(
                new \Magento\Framework\DataObject(
                    ['collection' => $imagesCollection, 'title' => $product->getName(), 'thumbnail' => $thumbnail]
                )
            );
        }
    }

    /**
     * Get all product images
     *
     * @param \Magento\Framework\DataObject $product
     * @param int $storeId
     * @return array
     */
    protected function _getAllProductImages($product, $storeId)
    {
        $product->setStoreId($storeId);
        $gallery = $this->mediaGalleryResourceModel->loadProductGalleryByAttributeId(
            $product,
            $this->mediaGalleryReadHandler->getAttribute()->getId()
        );

        $imagesCollection = [];
        if ($gallery) {
            foreach ($gallery as $image) {
                $imagesCollection[] = new \Magento\Framework\DataObject(
                    [
                        'url' => $this->getProductImageUrl($image['file'], $product),
                        'caption' => $image['label'] ? $image['label'] : $image['label_default'],
                    ]
                );
            }
        }

        return $imagesCollection;
    }

    /**
     * Get product image URL from image filename and path
     *
     * @param string $image
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    private function getProductImageUrl($image, $product)
    {
        $imgUrl = $this->catalogImageHelper
            ->init($product, 'product_page_image_large')
            ->setImageFile($image)
            ->getUrl();

        return $imgUrl;
    }
}
