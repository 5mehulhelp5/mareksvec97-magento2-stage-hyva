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
        $pageSize    = max(1, (int)($config['page_size'] ?? 24));
        $periodDays  = (int)($config['period_days'] ?? 90);
        $onlyInStock = (bool)($config['only_in_stock'] ?? true);
        $onlyVisible = (bool)($config['only_visible'] ?? true);

        $storeId = (int) $this->storeManager->getStore()->getId();

        // Buffer: načítaj viac bestseller ID, lebo časť odpadne po filtroch (visibility/stock/store)
        $bufferSize = max($pageSize * 5, $pageSize);

        // 1) Načítaj IDčka bestsellerov z reportu (zoradené podľa qty_ordered DESC)
        $productIds = $this->getBestsellerProductIds($storeId, $periodDays, $bufferSize);

        // Fallback: v niektorých obchodoch bývajú reporty v store_id=0 (All Store Views)
        if ($productIds === []) {
            $productIds = $this->getBestsellerProductIds(0, $periodDays, $bufferSize);
        }

        if ($productIds === []) {
            return [];
        }

        // 2) Načítaj produkty podľa týchto ID + aplikuj filtre
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

        // 3) Zachovaj poradie z reportu (aby to bolo reálne podľa qty_ordered)
        $collection->getSelect()->order(new Zend_Db_Expr(
            sprintf('FIELD(e.entity_id, %s)', implode(',', $productIds))
        ));

        // 4) Finálny rez až na konci
        $collection->setPageSize($pageSize);

        return $collection->getItems();
    }

    /**
     * Vráti zoradené product_id z bestseller reportu.
     */
    private function getBestsellerProductIds(int $storeId, int $periodDays, int $limit): array
    {
        $bestsellers = $this->bestsellersCollectionFactory->create();

        // POZN.: nepoužívame setStoreIds() - nie je dostupné vo všetkých verziách
        $bestsellers->addStoreFilter($storeId);
        $bestsellers->setPageSize($limit);

        // Ak je periodDays > 0, zúžime časové okno
        if ($periodDays > 0) {
            $toDate = $this->timezone->date();
            $fromDate = (clone $toDate)->modify(sprintf('-%d days', $periodDays));

            $bestsellers->setPeriod('day');
            $bestsellers->setDateRange(
                $fromDate->format('Y-m-d H:i:s'),
                $toDate->format('Y-m-d H:i:s')
            );
        }

        // Dôležité: zoradenie podľa predaja
        $bestsellers->getSelect()->order('qty_ordered DESC');

        $ids = array_values(array_unique($bestsellers->getColumnValues('product_id')));

        // bezpečnosť: odstráň prípadné prázdne hodnoty
        return array_values(array_filter($ids, static fn($v) => (int)$v > 0));
    }
}
