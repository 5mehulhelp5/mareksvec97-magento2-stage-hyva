<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Block\Review\Product\ListView;

use MageWorx\GeoIP\Model\Geoip;
use MageWorx\XReviewBase\Model\ConfigProvider;
use MageWorx\XReviewBase\Model\ResourceModel\Review;
use MageWorx\XReviewBase\Model\Review\ReviewList\Toolbar as ToolbarModel;
use MageWorx\XReviewBase\Model\Review\ReviewList\ToolbarMemorizer;
use MageWorx\XReviewBase\Model\Source\Filter as FilterConfig;
use MageWorx\XReviewBase\Model\Source\Sorting as OrderOptions;

/**
 * Review list toolbar
 *
 */
class Toolbar extends \Magento\Framework\View\Element\Template
{
    /**
     * Review collection
     *
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected $collection = null;

    /**
     * List of available order fields
     *
     * @var array
     */
    protected $availableOrder = null;

    /**
     * Is Expanded
     *
     * @var bool
     */
    protected $isExpanded = true;

    /**
     * Default Order field
     *
     * @var string
     */
    protected $orderField = null;

    /**
     * Default Filter field
     *
     * @var string
     */
    protected $filterField = null;

    /**
     * Default direction
     *
     * @var string
     */
    protected $direction;

    /**
     * Default value for filter by verified buyers
     *
     * @var string
     */
    protected $filterValue = ConfigProvider::DEFAULT_FILTER_VALUE;

    /**
     * Default value for filter by media
     *
     * @var string
     */
    protected $filterMediaValue = ConfigProvider::DEFAULT_FILTER_MEDIA_VALUE;

    /**
     * Default value for filter by location
     *
     * @var string
     */
    protected $filterLocationValue = ConfigProvider::DEFAULT_FILTER_LOCATION_VALUE;

    /**
     * @var string
     */
    protected $_template = 'MageWorx_XReviewBase::review/product/list/toolbar.phtml';

    /**
     * @var ToolbarModel
     */
    protected $toolbarModel;

    /**
     * @var Review
     */
    protected $reviewResource;

    /**
     * @var FilterConfig
     */
    protected $filterConfig;

    /**
     * @var \MageWorx\XReviewBase\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * @var \MageWorx\XReviewBase\Model\ReviewCollectionStatisticsProvider
     */
    protected $statisticsProvider;

    /**
     * @var Geoip
     */
    protected $geoIp;

    /**
     * @var OrderOptions
     */
    protected $orderOptions;

    /**
     * @var \MageWorx\XReviewBase\Model\LocationTextCreator
     */
    protected $locationTextCreator;

    /**
     * @var ToolbarMemorizer
     */
    private $toolbarMemorizer;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postDataHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    public function __construct(
        \MageWorx\GeoIP\Model\Geoip $geoIp,
        \Magento\Framework\View\Element\Template\Context $context,
        \MageWorx\XReviewBase\Model\ResourceModel\Review $reviewResource,
        \MageWorx\XReviewBase\Model\ConfigProvider $configProvider,
        \MageWorx\XReviewBase\Model\ReviewCollectionStatisticsProvider $statisticsProvider,
        \MageWorx\XReviewBase\Model\LocationTextCreator $locationTextCreator,
        FilterConfig $filterConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        ToolbarMemorizer $toolbarMemorizer,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Data\Form\FormKey $formKey,
        OrderOptions $orderOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->toolbarModel        = $toolbarModel;
        $this->urlEncoder          = $urlEncoder;
        $this->postDataHelper      = $postDataHelper;
        $this->toolbarMemorizer    = $toolbarMemorizer;
        $this->httpContext         = $httpContext;
        $this->formKey             = $formKey;
        $this->reviewResource      = $reviewResource;
        $this->filterConfig        = $filterConfig;
        $this->configProvider      = $configProvider;
        $this->statisticsProvider  = $statisticsProvider;
        $this->geoIp               = $geoIp;
        $this->orderOptions        = $orderOptions;
        $this->locationTextCreator = $locationTextCreator;
    }

    /**
     * Set collection to pager
     *
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        $this->collection->setCurPage($this->getCurrentPage());

        if ($this->getCurrentFilterValue() == ConfigProvider::FILTER_ENABLE) {
            $collection->addFieldToFilter('detail.is_verified', 1);
        }

        if ($this->getCurrentMediaFilterValue() == ConfigProvider::MEDIA_FILTER_ENABLE) {
            $this->reviewResource->addMediaExistsFilterToReviewCollection($this->collection);
        }

        if ($this->getCurrentLocationFilterValue() == ConfigProvider::LOCATION_FILTER_ENABLE
            && $this->getCurrentCountryCode()
        ) {
            $collection->addFieldToFilter('detail.location', $this->getCurrentCountryCode());
        }

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->collection->setPageSize($limit);
        }

        if ($this->getCurrentOrder()) {
            if (($this->getCurrentOrder()) == OrderOptions::SORT_FIELD_STAGE_VALUE) {
                $this->reviewResource->addAverageCustomerRatingToReviewCollection($this->collection);
            }

            $this->collection->unshiftOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }

        if (in_array(FilterConfig::FILTER_BY_MEDIA, $this->getAllowedFilters())) {
            $this->reviewResource->addImageCountToReviewCollection($this->collection);
            $this->collection->setFlag('mageworx_need_media_count', true);
        }

        if (in_array(FilterConfig::FILTER_BY_LOCATION, $this->getAllowedFilters())) {
            $this->collection->setFlag('mageworx_need_location_count', true);
        }

        if (in_array(FilterConfig::FILTER_BY_VERIFIED_CUSTOMER, $this->getAllowedFilters())) {
            $this->collection->setFlag('mageworx_need_customer_count', true);
        }

        return $this;
    }

    /**
     * Return review collection instance
     *
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Return current page from request
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->toolbarModel->getCurrentPage();
    }

    /**
     * Get grit products sort order field
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_current_grid_order');
        if ($order) {
            return $order;
        }

        $orders       = $this->getAvailableOrders();
        $defaultOrder = $this->getOrderField();

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            if (!empty($keys[0])) {
                $defaultOrder = $keys[0];
            }
        }

        $order = $this->toolbarMemorizer->getOrder();
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::ORDER_PARAM_NAME, $order, $defaultOrder);
        }
        $this->setData('_current_grid_order', $order);

        return $order;
    }

    /**
     * @return array
     */
    public function getAllowedFilters()
    {
        $filters = $this->configProvider->getAllowedToolbarFilters();

        if (!$this->configProvider->isDisplayImages()) {
            unset($filters[FilterConfig::FILTER_BY_MEDIA]);
        }

        if (!$this->getCurrentCountryCode()) {
            unset($filters[FilterConfig::FILTER_BY_LOCATION]);
        }

        return $filters;
    }

    /**
     * Retrieve current filter
     *
     * @return string
     */
    public function getCurrentFilterValue()
    {
        if (!in_array(FilterConfig::FILTER_BY_LOCATION, $this->getAllowedFilters())) {
            return $this->configProvider->getDefaultLocationFilterField();
        }

        $filterValue = $this->_getData('_current_grid_filter');
        if ($filterValue) {
            return $filterValue;
        }

        $values      = ['on', 'off'];
        $filterValue = strtolower((string)$this->toolbarMemorizer->getFilter());
        if (!$filterValue || !in_array($filterValue, $values)) {
            $filterValue = $this->filterValue;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::FILTER_PARAM_NAME, $filterValue, $this->filterValue);
        }

        $this->setData('_current_grid_filter', $filterValue);

        return $filterValue;
    }

    /**
     * Retrieve current media filter
     *
     * @return string
     */
    public function getCurrentMediaFilterValue()
    {
        if (!in_array(FilterConfig::FILTER_BY_MEDIA, $this->getAllowedFilters())) {
            return $this->configProvider->getDefaultMediaFilterField();
        }

        $filterValue = $this->_getData('_current_grid_filter_media');
        if ($filterValue) {
            return $filterValue;
        }

        $values      = ['on', 'off'];
        $filterValue = strtolower((string)$this->toolbarMemorizer->getMediaFilter());
        if (!$filterValue || !in_array($filterValue, $values)) {
            $filterValue = $this->filterMediaValue;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::FILTER_MEDIA_PARAM_NAME, $filterValue, $this->filterMediaValue);
        }

        $this->setData('_current_grid_filter_media', $filterValue);

        return $filterValue;
    }

    /**
     * Retrieve current location filter
     *
     * @return string
     */
    public function getCurrentLocationFilterValue()
    {
        if (!in_array(FilterConfig::FILTER_BY_LOCATION, $this->getAllowedFilters())) {
            return $this->configProvider->getDefaultLocationFilterField();
        }

        $filterValue = $this->_getData('_current_grid_filter_location');
        if ($filterValue) {
            return $filterValue;
        }

        $values      = ['on', 'off'];
        $filterValue = strtolower((string)$this->toolbarMemorizer->getLocationFilter());

        if (!$filterValue || !in_array($filterValue, $values)) {
            $filterValue = $this->filterLocationValue;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(
                ToolbarModel::FILTER_LOCATION_PARAM_NAME,
                $filterValue,
                $this->filterLocationValue
            );
        }

        $this->setData('_current_grid_filter_location', $filterValue);

        return $filterValue;
    }

    /**
     * Retrieve current direction
     *
     * @return string
     */
    public function getCurrentDirection()
    {
        $dir = $this->_getData('_current_grid_direction');
        if ($dir) {
            return $dir;
        }

        $this->direction = $this->configProvider->getDefaultSortDirection();

        $directions = ['asc', 'desc'];
        $dir        = strtolower((string)$this->toolbarMemorizer->getDirection());
        if (!$dir || !in_array($dir, $directions)) {
            $dir = $this->direction;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::DIRECTION_PARAM_NAME, $dir, $this->direction);
        }

        $this->setData('_current_grid_direction', $dir);

        return $dir;
    }

    /**
     * Set default sort direction
     *
     * @param string $dir
     * @return $this
     */
    public function setDefaultDirection($dir)
    {
        $dir = (string)$dir;
        if (in_array(strtolower($dir), ['asc', 'desc'])) {
            $this->direction = strtolower($dir);
        }

        return $this;
    }

    /**
     * Set default Order field
     *
     * @param string $field
     * @return $this
     */
    public function setDefaultOrder($field)
    {
        $this->loadAvailableOrders();
        if (isset($this->availableOrder[$field])) {
            $this->orderField = $field;
        }

        return $this;
    }

    /**
     * Retrieve available Order fields list
     *
     * @return array
     */
    public function getAvailableOrders()
    {
        $this->loadAvailableOrders();

        return $this->availableOrder;
    }

    /**
     * Remove order from available orders if exists
     *
     * @param string $order
     * @return $this
     */
    public function removeOrderFromAvailableOrders($order)
    {
        $this->loadAvailableOrders();
        if (isset($this->availableOrder[$order])) {
            unset($this->availableOrder[$order]);
        }

        return $this;
    }

    /**
     * Compare defined order field with current order field
     *
     * @param string $order
     * @return bool
     */
    public function isOrderCurrent($order)
    {
        return $order == $this->getCurrentOrder();
    }

    /**
     * Compare defined filter field with current filter field
     *
     * @param string $filter
     * @return bool
     */
    public function isFilterValueCurrent($filter)
    {
        return $filter == $this->getCurrentFilterValue();
    }

    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams                 = [];
        $urlParams['_current']     = true;
        $urlParams['_escape']      = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query']       = $params;

        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * Get pager encoded url.
     *
     * @param array $params
     * @return string
     */
    public function getPagerEncodedUrl($params = [])
    {
        return $this->urlEncoder->encode($this->getPagerUrl($params));
    }

    /**
     * Disable view switcher
     *
     * @return \MageWorx\XReviewBase\Block\Review\Product\ListView\Toolbar
     */
    public function disableViewSwitcher()
    {
        $this->_enableViewSwitcher = false;

        return $this;
    }

    /**
     * Enable view switcher
     *
     * @return \MageWorx\XReviewBase\Block\Review\Product\ListView\Toolbar
     */
    public function enableViewSwitcher()
    {
        $this->_enableViewSwitcher = true;

        return $this;
    }

    /**
     * Disable Expanded
     *
     * @return \MageWorx\XReviewBase\Block\Review\Product\ListView\Toolbar
     */
    public function disableExpanded()
    {
        $this->isExpanded = false;

        return $this;
    }

    /**
     * Enable Expanded
     *
     * @return \MageWorx\XReviewBase\Block\Review\Product\ListView\Toolbar
     */
    public function enableExpanded()
    {
        $this->isExpanded = true;

        return $this;
    }

    /**
     * Check is Expanded
     *
     * @return bool
     */
    public function isExpanded()
    {
        return $this->isExpanded;
    }

    /**
     * Retrieve default per page values
     *
     * @return string (comma separated)
     */
    public function getDefaultPerPageValue()
    {
        return $this->configProvider->getDefaultLimitPerPageValue();
    }

    /**
     * Retrieve available limits
     *
     * @return array
     */
    public function getAvailableLimit()
    {
        return $this->configProvider->getAvailableLimit();
    }

    /**
     * Get specified products limit display per page
     *
     * @return string
     */
    public function getLimit()
    {
        $limit = $this->_getData('_current_limit');
        if ($limit) {
            return $limit;
        }

        $limits       = $this->getAvailableLimit();
        $defaultLimit = $this->getDefaultPerPageValue();
        if (!$defaultLimit || !isset($limits[$defaultLimit])) {
            $keys         = array_keys($limits);
            $defaultLimit = $keys[0];
        }

        $limit = $this->toolbarMemorizer->getLimit();
        if (!$limit || !isset($limits[$limit])) {
            $limit = $defaultLimit;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::LIMIT_PARAM_NAME, $limit, $defaultLimit);
        }

        $this->setData('_current_limit', $limit);

        return $limit;
    }

    /**
     * Check if limit is current used in toolbar.
     *
     * @param int $limit
     * @return bool
     */
    public function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }

    /**
     * Pager number of items from which products started on current page.
     *
     * @return int
     */
    public function getFirstNum()
    {
        $collection = $this->getCollection();

        return $collection->getPageSize() * ($collection->getCurPage() - 1) + 1;
    }

    /**
     * Pager number of items products finished on current page.
     *
     * @return int
     */
    public function getLastNum()
    {
        $collection = $this->getCollection();

        return $collection->getPageSize() * ($collection->getCurPage() - 1) + $collection->count();
    }

    /**
     * Total number of products in current category.
     *
     * @return int
     */
    public function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }

    /**
     * Check if current page is the first.
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }

    /**
     * Return last page number.
     *
     * @return int
     */
    public function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('product_review_list.pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {

            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock->toHtml();
        }

        return '';
    }

    /**
     * Retrieve widget options in json format
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = [])
    {
        $options = [
            'direction'             => ToolbarModel::DIRECTION_PARAM_NAME,
            'order'                 => ToolbarModel::ORDER_PARAM_NAME,
            'limit'                 => ToolbarModel::LIMIT_PARAM_NAME,
            'filter'                => ToolbarModel::FILTER_PARAM_NAME,
            'mediaFilter'           => ToolbarModel::FILTER_MEDIA_PARAM_NAME,
            'locationFilter'        => ToolbarModel::FILTER_LOCATION_PARAM_NAME,
            'directionDefault'      => $this->direction ?: $this->configProvider->getDefaultSortDirection(),
            'orderDefault'          => $this->getOrderField(),
            'limitDefault'          => $this->configProvider->getDefaultLimitPerPageValue(),
            'filterDefault'         => $this->configProvider->getDefaultFilterField(),
            'mediaFilterDefault'    => $this->configProvider->getDefaultMediaFilterField(),
            'locationFilterDefault' => $this->configProvider->getDefaultLocationFilterField(),
            'url'                   => $this->getPagerUrl(),
            'formKey'               => $this->formKey->getFormKey(),
            'post'                  => $this->toolbarMemorizer->isMemorizingAllowed() ? true : false,
            'ajax'                  => $this->_request->isAjax()
        ];

        $options = array_replace_recursive($options, $customOptions);

        return json_encode($options);
    }

    /**
     * Get order field
     *
     * @return null|string
     */
    protected function getOrderField()
    {
        if ($this->orderField === null) {
            $this->orderField = $this->configProvider->getDefaultSortField();
        }

        return $this->orderField;
    }

    /**
     * Load Available Orders
     *
     * @return $this
     */
    private function loadAvailableOrders()
    {
        if ($this->availableOrder === null) {

            $availableOrders = $this->configProvider->getAllowedToolbarOrders();

            $this->availableOrder = [];

            foreach ($this->orderOptions->toArray() as $orderField => $orderName) {
                if (in_array($orderField, $availableOrders)) {
                    $this->availableOrder[$orderField] = $orderName;
                }
            }
        }

        return $this;
    }

    /**
     * Get filter field
     *
     * @return null|string
     */
    protected function getFilterField()
    {
        if ($this->filterField === null) {
            $this->filterField = $this->configProvider->getDefaultFilterField();
        }

        return $this->filterField;
    }

    /**
     * @return mixed
     */
    public function getCustomerCount()
    {
        return $this->statisticsProvider->getCustomerCount();
    }

    /**
     * @return mixed
     */
    public function getMediaCount()
    {
        return $this->statisticsProvider->getMediaCount();
    }

    /**
     * @return mixed
     */
    public function getLocationReviewCount()
    {
        return $this->statisticsProvider->getLocationReviewCount();
    }

    /**
     * @param string|null $class
     * @return string|null
     */
    public function getCurrentFlagImageHtml($class)
    {
        return $this->locationTextCreator->getFlagImageHtml(
            (string)$this->getCurrentCountryCode(),
            (string)$this->getCurrentCountryLabel(),
            (string)$class
        );
    }

    /**
     * @return string|null
     */
    protected function getCurrentCountryCode()
    {
        $data = $this->geoIp->getCurrentLocation();

        if (!empty($data['code'])) {
            return $data['code'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getCurrentCountryLabel()
    {
        $data = $this->geoIp->getCurrentLocation();

        if (!empty($data['region'])) {
            return $data['region'];
        }

        return null;
    }

    /**
     * @return \MageWorx\XReviewBase\Model\Media[]
     */
    public function getTopMediaItems()
    {
        $items = [];

        if ($this->configProvider->isDisplayImages()) {

            $firstItems = [];
            $allItems   = [];

            foreach ($this->getCollection() as $review) {
                if ($review->getMediaGallery()) {
                    $i = 0;
                    foreach ($review->getMediaGallery() as $mediaItem) {
                        if ($i == 0) {
                            $firstItems[] = $mediaItem;

                            if (count($firstItems) > 4) {
                                $useFirst = true;
                                break 2;
                            }
                        }
                        $allItems[] = $mediaItem;
                        $i++;
                    }
                }
            }

            if (!empty($useFirst)) {
                $items = $firstItems;
            } else {
                $items = array_slice($allItems, 0, 5);
            }
        }

        return $items;
    }
}
