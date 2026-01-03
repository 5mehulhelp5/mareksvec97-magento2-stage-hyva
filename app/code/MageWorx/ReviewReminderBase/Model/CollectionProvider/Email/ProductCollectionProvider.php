<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\CollectionProvider\Email;

use Exception;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogInventory\Helper\Stock;
use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\ResourceModel\Review\Summary;
use Magento\Review\Model\ResourceModel\Review\SummaryFactory;
use Magento\Review\Model\Review;

class ProductCollectionProvider
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Catalog product visibility
     *
     * @var ProductVisibility
     */
    protected $catalogProductVisibility;

    /**
     * Catalog config
     *
     * @var Config
     */
    protected $catalogConfig;

    /**
     * @var ProductStatus
     */
    protected $productStatus;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var SummaryFactory
     */
    protected $sumResourceFactory;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Stock
     */
    protected $stockHelper;

    /**
     * @var array
     */
    protected $collectionCache = [];

    /**
     * ProductCollectionProvider constructor.
     *
     * @param CollectionFactory $productCollectionFactory
     * @param ProductVisibility $catalogProductVisibility
     * @param ProductStatus $productStatus
     * @param Config $catalogConfig
     * @param Configuration $configuration
     * @param Stock $stockHelper
     * @param EventManager $eventManager
     * @param SummaryFactory $sumResourceFactory
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductVisibility $catalogProductVisibility,
        ProductStatus $productStatus,
        Config $catalogConfig,
        Configuration $configuration,
        Stock $stockHelper,
        EventManager $eventManager,
        SummaryFactory $sumResourceFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->catalogConfig            = $catalogConfig;
        $this->productStatus            = $productStatus;
        $this->configuration            = $configuration;
        $this->stockHelper              = $stockHelper;
        $this->eventManager             = $eventManager;
        $this->sumResourceFactory       = $sumResourceFactory;
    }

    /**
     * The $productIds param is needed only for first load. Then the collection will be retrieve from cache.
     *
     * @param int $storeId
     * @param array $productIds
     * @return ProductCollection|null
     * @throws LocalizedException
     */
    public function getCollection($storeId, array $productIds = [])
    {
        if (!array_key_exists($storeId, $this->collectionCache)) {

            if ($productIds) {
                $productCollection = $this->productCollectionFactory->create();

                $productCollection->setStoreId($storeId)
                                  ->addIdFilter($productIds)
                                  ->addStoreFilter($storeId)
                                  ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
                                  ->addAttributeToFilter(
                                      'status',
                                      [
                                          'in' => $this->productStatus->getVisibleStatusIds()
                                      ]
                                  )
                                  ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
                                  ->addUrlRewrite();

                if ($this->configuration->isShowOutOfStock($storeId) === false) {
                    $this->stockHelper->addInStockFilterToCollection($productCollection);
                }

                /** @var Summary $sumResource */
                $sumResource = $this->sumResourceFactory->create();

                $sumResource->appendSummaryFieldsToCollection(
                    $productCollection,
                    $storeId,
                    Review::ENTITY_PRODUCT_CODE
                );

                $this->eventManager->dispatch(
                    'mageworx_reviewreminderbase_email_product_collection_load_before',
                    ['collection' => $productCollection, 'productIds' => $productIds]
                );

                if ($productCollection->isLoaded()) {
                    throw new Exception('Incorrect state: product collection already loaded');
                }

                // loading here
                $productCollection->addMediaGalleryData();

                $this->eventManager->dispatch(
                    'mageworx_reviewreminderbase_email_product_collection_load_after',
                    ['collection' => $productCollection]
                );

            } else {
                $productCollection = null;
            }

            $this->collectionCache[$storeId] = $productCollection;
        }

        return $this->collectionCache[$storeId];
    }
}
