<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Block\Review\Widget;

use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\ResourceModel\Review\SummaryFactory;
use Magento\Widget\Block\BlockInterface;
use MageWorx\XReviewBase\Model\ConfigProvider;
use Magento\Catalog\Helper\Data as CatalogHelper;

class ReviewsList extends \Magento\Framework\View\Element\Template implements BlockInterface, IdentityInterface
{
    /**
     * Default value for reviews count that will be shown
     */
    const DEFAULT_REVIEWS_COUNT = 10;

    /**
     * Default value for reviews per page
     */
    const DEFAULT_REVIEWS_PER_PAGE = 5;

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    const ATTRIBUTE_PRODUCT_TITLE     = 'product_title';
    const ATTRIBUTE_PRODUCT_IMAGE     = 'product_image';
    const ATTRIBUTE_REVIEW_BY         = 'review_by';
    const ATTRIBUTE_GEOIP_LOCATION    = 'geoip_location';
    const ATTRIBUTE_REVIEW_DATE       = 'review_date';
    const ATTRIBUTE_I_RECOMMEND_LABEL = 'i_recommend_label';

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $pager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $converter;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    protected $json;

    /**
     * @var \Magento\Framework\Url\EncoderInterface|null
     */
    protected $urlEncoder;

    /**
     * @var ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var SummaryFactory
     */
    protected $sumResourceFactory;

    /**
     * @var ReviewCollectionFactory
     */
    protected $reviewCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * @var ProductCollection
     */
    protected $productCollection;

    /**
     * @var ReviewCollection|null
     */
    protected $reviewCollection;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @var array|null
     */
    protected $reviewAttributes;

    /**
     * ReviewsList constructor.
     *
     * @param ImageBuilder $imageBuilder
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param SummaryFactory $sumResourceFactory
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param Json $json
     * @param ConfigProvider $configProvider
     * @param CatalogHelper $catalogHelper
     * @param array $data
     */
    public function __construct(
        ImageBuilder $imageBuilder,
        \Magento\Catalog\Model\Config $catalogConfig,
        SummaryFactory $sumResourceFactory,
        ReviewCollectionFactory $reviewCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        Json $json,
        ConfigProvider $configProvider,
        CatalogHelper $catalogHelper,
        array $data = []
    ) {
        $this->imageBuilder             = $imageBuilder;
        $this->catalogConfig            = $catalogConfig;
        $this->sumResourceFactory       = $sumResourceFactory;
        $this->reviewCollectionFactory  = $reviewCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext              = $httpContext;
        $this->sqlBuilder               = $sqlBuilder;
        $this->rule                     = $rule;
        $this->conditionsHelper         = $conditionsHelper;
        $this->json                     = $json;
        $this->configProvider           = $configProvider;
        $this->catalogHelper            = $catalogHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData(
            [
                'cache_lifetime' => 86400,
                'cache_tags'     => [
                    \Magento\Catalog\Model\Product::CACHE_TAG,
                ],
            ]
        );
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');

        return [
            'MAGEWORX_XREVIEW_REVIEW_LIST_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            (int)$this->getRequest()->getParam($this->getData('page_var_name'), 1),
            $this->getReviewsPerPage(),
            $this->getReviewsCount(),
            $conditions,
            $this->json->serialize($this->getRequest()->getParams()),
            $this->getTemplate(),
            $this->getTitle()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->createProductCollection());
        $this->setReviewCollection($this->createReviewCollection());

        return parent::_beforeToHtml();
    }

    /**
     * @param ProductCollection $collection
     * @return void
     */
    protected function setProductCollection(ProductCollection $collection)
    {
        $this->productCollection = $collection;
    }

    /**
     * @return ProductCollection
     */
    protected function getProductCollection(): ProductCollection
    {
        return $this->productCollection;
    }

    /**
     * @param ReviewCollection $collection
     * @return void
     */
    protected function setReviewCollection(ReviewCollection $collection)
    {
        $this->reviewCollection = $collection;
    }

    /**
     * @return ReviewCollection|null
     */
    protected function getReviewCollection(): ?ReviewCollection
    {
        return $this->reviewCollection;
    }

    /**
     * @return ReviewCollection|null
     */
    public function getCollection(): ?ReviewCollection
    {
        return $this->getReviewCollection();
    }

    /**
     * Prepare and return product collection
     *
     * @return ProductCollection
     */
    protected function createProductCollection(): ProductCollection
    {
        /** @var $collection ProductCollection */
        $collection = $this->productCollectionFactory->create();

        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }

        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite()
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc');

        if ($this->getData('from_same_category') && $this->catalogHelper->getCategory()) {
            $category      = $this->catalogHelper->getCategory();
            $categoryIds   = $category->getChildren() ? explode(',', $category->getChildren()) : [];
            $categoryIds[] = $category->getId();

            $collection->addCategoriesFilter(['in' => $categoryIds]);
        }

        /** @var \Magento\Review\Model\ResourceModel\Review\Summary $sumResource */
        $sumResource = $this->sumResourceFactory->create();

        $sumResource->appendSummaryFieldsToCollection(
            $collection,
            $this->_storeManager->getStore()->getId(),
            \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE
        );

        $select = $collection->getSelect();
        $select->where('review_summary.rating_summary > 0');

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $collection->distinct(true);

        return $collection;
    }

    /**
     * @return ReviewCollection
     */
    protected function createReviewCollection(): ReviewCollection
    {
        /** @var ReviewCollection $tempReviewCollection */
        $tempReviewCollection = $this->reviewCollectionFactory->create();

        $tempReviewCollection
            ->addStoreFilter($this->getData('store_id'))
            ->addFieldToFilter('entity_code', \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
            ->addFieldToFilter('status_id', ['in' => \Magento\Review\Model\Review::STATUS_APPROVED])
            ->addFieldToFilter('entity_pk_value', ['in' => $this->getProductCollection()->getAllIds()])
            ->join(
                $tempReviewCollection->getTable('review_entity'),
                'main_table.entity_id=' . $tempReviewCollection->getTable('review_entity') . '.entity_id',
                ['entity_code']
            );

        $select = $tempReviewCollection->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->group('entity_pk_value');
        $select->columns(['entity_pk_value', new \Zend_Db_Expr('GROUP_CONCAT(main_table.review_id separator "|")')]);

        $reviewsByProducts = $tempReviewCollection->getConnection()->fetchPairs($select);

        $reviewIds = $this->getRandomReviewIds($reviewsByProducts);

        /** @var ReviewCollection $reviewCollection */
        $reviewCollection = $this->reviewCollectionFactory->create();

        if ($this->configProvider->isDisplayLocation() && $this->configProvider->getLocationTemplate()) {
            $reviewCollection->setFlag('mageworx_need_location_text', true);
        }

        $reviewCollection->addFieldToFilter('main_table.review_id', ['in' => $reviewIds])
                         ->setPageSize($this->getPageSize())
                         ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));

        $reviewCollection->getSelect()->order(
            new \Zend_Db_Expr('FIELD(main_table.review_id, ' . "'" . implode("','", $reviewIds) . "'" . ')')
        );

        return $reviewCollection;
    }

    /**
     * @param array $reviewsByProducts
     * @return array
     */
    protected function getRandomReviewIds(array $reviewsByProducts): array
    {
        foreach ($reviewsByProducts as $productId => $reviewsAsString) {
            $reviewIds = explode('|', $reviewsAsString);

            shuffle($reviewIds);

            $reviewsByProducts[$productId] = $reviewIds;
        }

        $ids = [];

        for ($i = 0; $i < $this->getReviewsCount(); $i++) {
            foreach ($reviewsByProducts as &$reviewsIds) {
                if ($reviewsIds) {
                    $ids[] = array_shift($reviewsIds);
                }

                if (count($ids) == $this->getReviewsCount()) {
                    break 2;
                }
            }
        }

        return $ids;
    }

    /**
     * Get conditions
     *
     * @return \Magento\Rule\Model\Condition\Combine
     */
    protected function getConditions(): \Magento\Rule\Model\Condition\Combine
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])
                && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
            ) {
                $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
            }
        }

        $this->rule->loadPost(['conditions' => $conditions]);

        return $this->rule->getConditions();
    }

    /**
     * Retrieve how many reviews should be displayed
     *
     * @return int
     */
    public function getReviewsCount(): int
    {
        if ($this->hasData('reviews_count')) {
            return (int)$this->getData('reviews_count');
        }

        if (null === $this->getData('reviews_count')) {
            $this->setData('reviews_count', self::DEFAULT_REVIEWS_COUNT);
        }

        return (int)$this->getData('reviews_count');
    }

    /**
     * Retrieve how many reviews should be displayed
     *
     * @return int
     */
    public function getReviewsPerPage(): int
    {
        if (!$this->hasData('reviews_per_page')) {
            $this->setData('reviews_per_page', self::DEFAULT_REVIEWS_PER_PAGE);
        }

        return (int)$this->getData('reviews_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager(): bool
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }

        return (bool)$this->getData('show_pager');
    }

    /**
     * Retrieve how many reviews should be displayed on page
     *
     * @return int
     */
    protected function getPageSize(): int
    {
        return $this->showPager() ? $this->getReviewsPerPage() : $this->getReviewsCount();
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml(): string
    {
        if ($this->showPager() && $this->getReviewCollection()->getSize() > $this->getReviewsPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    \Magento\Catalog\Block\Product\Widget\Html\Pager::class,
                    $this->getWidgetPagerBlockName()
                );

                $this->pager->setUseContainer(true)
                            ->setShowAmounts(true)
                            ->setShowPerPage(false)
                            ->setPageVarName($this->getData('page_var_name'))
                            ->setLimit($this->getReviewsPerPage())
                            ->setTotalLimit($this->getReviewsCount())
                            ->setCollection($this->getReviewCollection());
            }

            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }

        return '';
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities(): array
    {
        $identities = [];
        if ($this->getReviewCollection()) {
            foreach ($this->getReviewCollection() as $review) {
                if ($review instanceof IdentityInterface) {
                    $identities = array_merge($identities, $review->getIdentities());
                }
            }
        }

        return $identities ?: [\Magento\Review\Model\Review::CACHE_TAG];
    }

    /**
     * Get value of widgets' title parameter
     *
     * @return mixed|string
     */
    public function getTitle(): string
    {
        return (string)$this->getData('title');
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return Product
     */
    public function getProductByReview(\Magento\Review\Model\Review $review): \Magento\Catalog\Model\Product
    {
        if (!$this->getProductCollection()->isLoaded()) {
            $this->getProductCollection()->addAttributeToFilter(
                'entity_id',
                ['in' => $this->getReviewCollection()->getColumnValues('entity_pk_value')]
            );
        }

        return $this->getProductCollection()->getItemByColumnValue('entity_id', $review->getEntityPkValue());
    }

    /**
     * Get widget block name
     *
     * @return string
     */
    private function getWidgetPagerBlockName(): string
    {
        $pageName       = $this->getData('page_var_name');
        $pagerBlockName = 'mageworx.xreview.widget.reviews.list.pager';

        if (!$pageName) {
            return $pagerBlockName;
        }

        return $pagerBlockName . '.' . $pageName;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = []): \Magento\Catalog\Block\Product\Image
    {
        return $this->imageBuilder->create($product, $imageId, $attributes);
    }

    /**
     * @return array
     */
    public function getReviewAttributes(): array
    {
        if (isset($this->reviewAttributes)) {
            return $this->reviewAttributes;
        }

        $this->reviewAttributes = explode(',', (string)$this->getData('review_attributes'));

        return $this->reviewAttributes;
    }

    /**
     * @return bool
     */
    public function allowProductTitle(): bool
    {
        return in_array(self::ATTRIBUTE_PRODUCT_TITLE, $this->getReviewAttributes());
    }

    /**
     * @return bool
     */
    public function allowProductImage(): bool
    {
        return in_array(self::ATTRIBUTE_PRODUCT_IMAGE, $this->getReviewAttributes());
    }

    /**
     * @return bool
     */
    public function allowReviewBy(): bool
    {
        return in_array(self::ATTRIBUTE_REVIEW_BY, $this->getReviewAttributes());
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return bool
     */
    public function allowGeoIpLocation(\Magento\Review\Model\Review $review): bool
    {
        if ($this->configProvider->isDisplayLocation()
            && $review->getLocationText()
            && in_array(self::ATTRIBUTE_GEOIP_LOCATION, $this->getReviewAttributes())
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function allowReviewDate(): bool
    {
        return in_array(self::ATTRIBUTE_REVIEW_DATE, $this->getReviewAttributes());
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return bool
     */
    public function allowRecommendLabel(\Magento\Review\Model\Review $review): bool
    {
        if ($this->configProvider->isDisplayRecommendLabel()
            && $review->getIsRecommend()
            && in_array(self::ATTRIBUTE_I_RECOMMEND_LABEL, $this->getReviewAttributes())
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getReviewsUrl(Product $product): string
    {
        return $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]) . '#reviews';
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return string
     */
    public function getReviewDetail(\Magento\Review\Model\Review $review): string
    {
        $detail = $review->getDetail();
        $limit  = $this->configProvider->getWidgetReviewCharactersLimit() + 3;

        return $detail ? mb_strimwidth($detail, 0, $limit, "...") : '';
    }
}
