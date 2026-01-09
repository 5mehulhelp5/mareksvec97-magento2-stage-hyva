<?php

declare(strict_types=1);

namespace BigConnect\ProductSlider\Model\Source;

use BigConnect\ProductSlider\Api\ProductSourceInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestsellersCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Db_Expr;

class BestsellersSource implements ProductSourceInterface
{
    private BestsellersCollectionFactory $bestsellersCollectionFactory;
    private ProductCollectionFactory $productCollectionFactory;
    private StoreManagerInterface $storeManager;
    private Visibility $visibility;
    private StockHelper $stockHelper;
    private TimezoneInterface $timezone;

    public function __construct(
        BestsellersCollectionFactory $bestsellersCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        Visibility $visibility,
        StockHelper $stockHelper,
        TimezoneInterface $timezone
    ) {
        $this->bestsellersCollectionFactory = $bestsellersCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->visibility = $visibility;
        $this->stockHelper = $stockHelper;
        $this->timezone = $timezone;
    }

    public function getCode(): string
    {
        return 'bestsellers';
    }

    public function getLabel(): string
    {
        return 'Bestsellers';
    }

    public function getItems(array $config): array
    {
        $pageSize = (int)($config['page_size'] ?? 8);
        $periodDays = (int)($config['period_days'] ?? 30);
        $onlyInStock = (bool)($config['only_in_stock'] ?? true);
        $onlyVisible = (bool)($config['only_visible'] ?? true);

        $storeId = (int)$this->storeManager->getStore()->getId();

        $bestsellers = $this->bestsellersCollectionFactory->create();
        $bestsellers->setStoreIds([$storeId]);
        $bestsellers->addStoreFilter($storeId);
        $bestsellers->setPageSize($pageSize);

        if ($periodDays > 0) {
            $toDate = $this->timezone->date();
            $fromDate = (clone $toDate)->modify(sprintf('-%d days', $periodDays));
            $bestsellers->setPeriod('day');
            $bestsellers->setDateRange(
                $fromDate->format('Y-m-d H:i:s'),
                $toDate->format('Y-m-d H:i:s')
            );
        }

        $productIds = array_values(array_unique($bestsellers->getColumnValues('product_id')));

        if ($productIds === []) {
            return [];
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addIdFilter($productIds);
        $collection->addStoreFilter($storeId);
        $collection->addAttributeToSelect([
            'name',
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'small_image',
        ]);
        $collection->addUrlRewrite();
        $collection->setPageSize($pageSize);

        if ($onlyVisible) {
            $collection->setVisibility($this->visibility->getVisibleInCatalogIds());
        }

        if ($onlyInStock) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }

        $collection->getSelect()->order(new Zend_Db_Expr(
            sprintf('FIELD(e.entity_id, %s)', implode(',', $productIds))
        ));

        return $collection->getItems();
    }
}
