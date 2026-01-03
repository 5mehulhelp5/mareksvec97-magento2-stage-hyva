<?php

namespace BigConnect\Badges\Plugin\Product;

use Magento\Catalog\Controller\Product\View as MagentoView;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ResourceModel\Category\Collection;

class View {
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var PageFactory
     */
    private $resultPage;

    /**
     * View constructor.
     * @param StoreManager $storeManager
     * @param Registry $registry
     * @param Collection $collection
     * @param PageFactory $resultPage
     */
    public function __construct(
        StoreManager $storeManager,
        Registry $registry,
        Collection $collection,
        PageFactory $resultPage
	) {
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->collection = $collection;
        $this->resultPage = $resultPage;
    }


    /**
     * @return Product
     * @throws LocalizedException
     */
    private function getProduct() {
        if (is_null($this->product)) {
            $product = $this->registry->registry('product');

            if (!$product || !$product->getId()) {
                throw new LocalizedException(__('Failed to initialize product'));
            }

            $this->product = $product;
        }

        return $this->product;
    }
	
    public function getDiscountPercent(Product $product) {
        try {
            $priceCode = \Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE;
            $finalPriceCode = \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE;
            $price = $product->getPriceInfo()->getPrice($priceCode)->getAmount()->getValue();
            $finalPrice = $product->getPriceInfo()->getPrice($finalPriceCode)->getAmount()->getValue();
            
            if (!$finalPrice || !$price) {
                return false;
            }

            $discountPercent = 100 - round($finalPrice / $price * 100, 2, PHP_ROUND_HALF_DOWN);

            if ($finalPrice < $price && $discountPercent < 100) {
                return sprintf('%d%%', $discountPercent);
            }
        } catch (\Exception $exception) {
            return false;
        }

        return false;
    }
	
	
}