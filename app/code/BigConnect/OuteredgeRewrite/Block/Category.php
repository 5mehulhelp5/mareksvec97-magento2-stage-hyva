<?php

namespace BigConnect\OuteredgeRewrite\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

class Category extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ListProduct
     */
    protected $listProductBlock;

    /**
     * @var AbstractCollection
     */
    protected $_productCollection = null;
    protected $_imageHelper;
    protected $_storeManager;
    protected $_categoryRepository;

    public function __construct(
        ListProduct $listProductBlock,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        Context $context,
        array $data = [])
    {
        $this->listProductBlock = $listProductBlock;
        $this->_imageHelper = $imageHelper;
        $this->_storeManager = $storeManager;
        $this->_categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    public function getSchemaJson()
    {
        $collection = $this->listProductBlock->getLoadedProductCollection();
        return json_encode($this->getSchemaData($collection), JSON_UNESCAPED_SLASHES);
    }

    public function getSchemaData(AbstractCollection $productCollection)
    {
        if (!$this->_productCollection) {
            $this->_productCollection = $productCollection;
        }

        $listData = [];
        $productData= [];	

        $i = 1;
        foreach ($this->_productCollection as $product) {
            $imagePath = $product->getData('thumbnail');
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $imagePath;

            $aggregateRatingData[] = [
                "@type" => "AggregateRating",
                "ratingValue" => "4.8", // Get currency from store
                "reviewCount" => "16" // Use standard Magento 2 method
            ];  

            $offersData[] = [
                "@type" => "Offer",
                "priceCurrency" => $this->_storeManager->getStore()->getCurrentCurrency()->getCode(), // Get currency from store
                "price" => $product->getPrice(), // Use standard Magento 2 method
                "availability" => "https://schema.org/InStock"
            ];     

            $productData = [
                "@type" => "Product",
                "name" => $product->getName(),
                "category" => $this->getCategoryNameByProduct($product),
                "url" => $product->getProductUrl(),
                "image" => $imageUrl,
                //"aggregateRating" => $aggregateRatingData,
                "offers" => $offersData
            ];

            // Here you can continue to add other attributes like "height", "width", etc.

            $listData[] = [
                "@context" => "https://schema.org/",
                "@type" => "ListItem",
                "position" => $i++,
                "item" => $productData
            ];
        }

        $data = [
            "@type" => "ItemList",
            "itemListElement" => $listData
        ];

        return $data;
    }
    
    public function getCategoryNameByProduct($product)
    {
        $categoryIds = $product->getCategoryIds();
        if (count($categoryIds)) {
            $categoryId = reset($categoryIds);
            try {
                $category = $this->_categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
                return $category->getName();
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // Log exception or handle error
                return null; // or some default value
            }
        }
        return null;
    }
    
}
