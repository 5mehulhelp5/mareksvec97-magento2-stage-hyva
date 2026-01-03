<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\Converter\Component;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as HelperImage;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\Component\Product as ReminderProduct;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\Component\ProductFactory;
use MageWorx\ReviewReminderBase\Model\ReminderConfigReader;

class ComponentProductConverter
{
    /**
     * @var ProductFactory
     */
    protected $reminderProductFactory;

    /**
     * @var HelperImage
     */
    protected $helperImage;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ReminderConfigReader
     */
    protected $configReader;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ComponentProductConverter constructor.
     *
     * @param ProductFactory $componentProductFactory
     * @param StoreManagerInterface $storeManager
     * @param HelperImage $helperImage
     * @param ReminderConfigReader $configReader
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ProductFactory $componentProductFactory,
        StoreManagerInterface $storeManager,
        HelperImage $helperImage,
        ReminderConfigReader $configReader,
        UrlInterface $urlBuilder
    ) {
        $this->reminderProductFactory = $componentProductFactory;
        $this->storeManager           = $storeManager;
        $this->helperImage            = $helperImage;
        $this->configReader           = $configReader;
        $this->urlBuilder             = $urlBuilder;
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return ReminderProduct
     */
    public function convertOrderItemToComponentProduct(OrderItemInterface $orderItem): ReminderProduct
    {
        /** @var Product $componentProduct */
        $componentProduct = $this->reminderProductFactory->create();

        $componentProduct->setData(
            [
                'product_id' => $orderItem->getProductId(),
                'name'       => $orderItem->getName(),
                'sales_sku'  => $orderItem->getSku(),
            ]
        );

        return $componentProduct;
    }

    /**
     * @param ProductInterface $product
     * @param ReminderProduct $reminderProduct
     * @return ReminderProduct
     * @throws NoSuchEntityException
     */
    public function fillComponentProductByProduct(
        ProductInterface $product,
        ReminderProduct $reminderProduct
    ): ReminderProduct {
        $reminderProduct->addData($this->getRatingSummaryData($product));
        $reminderProduct->addData(['url' => $this->getStoreUrl($product)]);

        $imageUrl = $this->helperImage->init($product, 'product_page_image_small')
                                      ->setImageFile($product->getSmallImage())
                                      ->resize(82)
                                      ->getUrl();

        $reminderProduct->addData(['image_url' => $imageUrl]);

        return $reminderProduct;
    }

    /**
     * Output schema:
     * ['rating_summary' => n, 'reviews_count' => m]
     *
     * @param ProductInterface $product
     * @return array
     */
    protected function getRatingSummaryData(ProductInterface $product): array
    {
        $ratingSummary = $product->getRatingSummary();
        $reviewCount   = 0;

        if ($ratingSummary) {
            // Magento < 2.3.3
            if ($ratingSummary instanceof DataObject) {
                $ratingSummary = $ratingSummary->getData('rating_summary');
                $reviewCount   = $ratingSummary->getData('reviews_count');
            } // Magento >= 2.3.3
            else {
                $reviewCount = $product->getData('reviews_count');
            }
        }

        return ['rating_summary' => $ratingSummary, 'reviews_count' => $reviewCount];
    }

    /**
     * @param ProductInterface $product
     * @param string $type
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getStoreUrl(ProductInterface $product, string $type = UrlInterface::URL_TYPE_LINK): string
    {
        /** @var Store $store */
        $store    = $this->storeManager->getStore($product->getStoreId());
        $isSecure = $store->isUrlSecure();

        $url = rtrim($store->getBaseUrl($type, $isSecure), '/') . '/' . ltrim($product['request_path'], '/');

        $params = [];

        if (!$store->isUseStoreInUrl()) {
            $params['___store'] = $store->getCode();
        }

        $params = array_merge($params, $this->configReader->getUtmParams($store->getId()));
        $query  = $params ? '?' . http_build_query($params) : '';

        return $url . $query . '#review-form';
    }
}
