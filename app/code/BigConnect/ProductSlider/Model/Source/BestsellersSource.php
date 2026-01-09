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
        $pageSize    = max(1, (int)($config['page_size'] ?? 8));
        $periodDays  = (int)($config['period_days'] ?? 30);
        $onlyInStock = (bool)($config['only_in_stock'] ?? true);
        $onlyVisible = (bool)($config['only_visible'] ?? true);

        $storeId = (int)$this->storeManager->getStore()->getId();

        // Buffer: vezmeme viac kandidátov, lebo časť odpadne na visibility/stock/store filtroch
        $bufferSize = max($pageSize * 6, $pageSize);

        // Najprv skús podľa konkrétneho storeId, potom fallback na 0 (All Store Views)
        $productIds = $this->getBestsellerProductIdsSummed($storeId, $periodDays, $bufferSize);
        if ($productIds === []) {
            $productIds = $this->getBestsellerProductIdsSummed(0, $periodDays, $bufferSize);
        }

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

        if ($onlyVisible) {
            $collection->setVisibility($this->visibility->getVisibleInCatalogIds());
        }

        if ($onlyInStock) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }

        // Zachovaj poradie z reportu (už je zoradené podľa SUM(qty_ordered) DESC)
        $collection->getSelect()->order(new Zend_Db_Expr(
            sprintf('FIELD(e.entity_id, %s)', implode(',', $productIds))
        ));

        // finálny rez až tu
        $collection->setPageSize($pageSize);

        return $collection->getItems();
    }

    /**
     * Vráti product_id zoradené podľa SUM(qty_ordered) za zvolené obdobie.
     * Stabilné riešenie: vlastný SELECT nad sales_bestsellers_aggregated_daily
     * (keď používaš period "day").
     */
    private function getBestsellerProductIdsSummed(int $storeId, int $periodDays, int $limit): array
    {
        $bestsellers = $this->bestsellersCollectionFactory->create();
        $conn = $bestsellers->getConnection();

        $bsTable  = $bestsellers->getTable('sales_bestsellers_aggregated_daily');
        $relTable = $bestsellers->getTable('catalog_product_relation');

        $select = $conn->select()
            ->from(['bs' => $bsTable], [])
            ->joinLeft(
                ['rel' => $relTable],
                'rel.child_id = bs.product_id',
                []
            )
            ->columns([
                // ak je product simple s parentom, použij parent_id, inak product_id
                'display_id' => new Zend_Db_Expr('COALESCE(rel.parent_id, bs.product_id)'),
                'total_qty'  => new Zend_Db_Expr('SUM(bs.qty_ordered)')
            ])
            ->where('bs.store_id = ?', $storeId)
            ->group(new Zend_Db_Expr('COALESCE(rel.parent_id, bs.product_id)'))
            ->order('total_qty DESC')
            ->limit($limit);

        if ($periodDays > 0) {
            $toDate = $this->timezone->date();
            $fromDate = (clone $toDate)->modify(sprintf('-%d days', $periodDays));

            $select->where('bs.period >= ?', $fromDate->format('Y-m-d'))
                   ->where('bs.period <= ?', $toDate->format('Y-m-d'));
        }

        $rows = $conn->fetchAll($select);

        $ids = [];
        foreach ($rows as $row) {
            $id = (int)($row['display_id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return $ids;
    }
}
