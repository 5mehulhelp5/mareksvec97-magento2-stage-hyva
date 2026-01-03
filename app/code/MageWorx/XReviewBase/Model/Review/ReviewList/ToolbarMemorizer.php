<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\XReviewBase\Model\Review\ReviewList;

use Magento\Catalog\Model\Session as CatalogSession;
use MageWorx\XReviewBase\Model\ConfigProvider;

/**
 * Class ToolbarMemorizer
 *
 * Responds for saving toolbar settings to catalog session
 */
class ToolbarMemorizer
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var CatalogSession
     */
    private $catalogSession;

    /**
     * @var Toolbar
     */
    private $toolbarModel;

    /**
     * @var string|bool
     */
    private $order;

    /**
     * @var string|bool
     */
    private $filter;

    /**
     * @var string|bool
     */
    private $mediaFilter;

    /**
     * @var string|bool
     */
    private $locationFilter;

    /**
     * @var string|bool
     */
    private $direction;

    /**
     * @var string|bool
     */
    private $limit;

    /**
     * @var bool
     */
    private $isMemorizingAllowed;

    /**
     * ToolbarMemorizer constructor.
     *
     * @param Toolbar $toolbarModel
     * @param CatalogSession $catalogSession
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        Toolbar $toolbarModel,
        CatalogSession $catalogSession,
        ConfigProvider $configProvider
    ) {
        $this->toolbarModel = $toolbarModel;
        $this->catalogSession = $catalogSession;
        $this->configProvider = $configProvider;
    }

    /**
     * Get sort order
     *
     * @return string|bool
     */
    public function getOrder()
    {
        if ($this->order === null) {
            $this->order = $this->toolbarModel->getOrder() ??
                ($this->isMemorizingAllowed() ? $this->catalogSession->getData(Toolbar::ORDER_PARAM_NAME) : null);
        }

        return $this->order;
    }

    /**
     * Get verified buyer filter
     *
     * @return string|bool
     */
    public function getFilter()
    {
        if ($this->filter === null) {
            $this->filter = $this->toolbarModel->getFilter() ??
                ($this->isMemorizingAllowed() ? $this->catalogSession->getData(Toolbar::FILTER_PARAM_NAME) : null);
        }

        return $this->filter;
    }

    /**
     * Get media filter
     *
     * @return string|bool
     */
    public function getMediaFilter()
    {
        if ($this->mediaFilter === null) {
            $this->mediaFilter = $this->toolbarModel->getMediaFilter() ??
                ($this->isMemorizingAllowed() ? $this->catalogSession->getData(
                    Toolbar::FILTER_MEDIA_PARAM_NAME
                ) : null);
        }

        return $this->mediaFilter;
    }

    /**
     * Get location filter
     *
     * @return string|bool
     */
    public function getLocationFilter()
    {
        if ($this->locationFilter === null) {
            $this->locationFilter = $this->toolbarModel->getLocationFilter() ??
                ($this->isMemorizingAllowed() ? $this->catalogSession->getData(
                    Toolbar::FILTER_LOCATION_PARAM_NAME
                ) : null);
        }

        return $this->locationFilter;
    }

    /**
     * Get sort direction
     *
     * @return string|bool
     */
    public function getDirection()
    {
        if ($this->direction === null) {
            $this->direction = $this->toolbarModel->getDirection() ??
                ($this->isMemorizingAllowed() ? $this->catalogSession->getData(Toolbar::DIRECTION_PARAM_NAME) : null);
        }

        return $this->direction;
    }

    /**
     * Get products per page limit
     *
     * @return string|bool
     */
    public function getLimit()
    {
        if ($this->limit === null) {
            $this->limit = $this->toolbarModel->getLimit() ??
                ($this->isMemorizingAllowed() ? $this->catalogSession->getData(Toolbar::LIMIT_PARAM_NAME) : null);
        }

        return $this->limit;
    }

    /**
     * Method to save all catalog parameters in catalog session
     *
     * @return void
     */
    public function memorizeParams()
    {
        if (!$this->catalogSession->getParamsMemorizeDisabled() && $this->isMemorizingAllowed()) {
            $this->memorizeParam(Toolbar::ORDER_PARAM_NAME, $this->getOrder())
                 ->memorizeParam(Toolbar::DIRECTION_PARAM_NAME, $this->getDirection())
                 ->memorizeParam(Toolbar::LIMIT_PARAM_NAME, $this->getLimit())
                 ->memorizeParam(Toolbar::FILTER_PARAM_NAME, $this->getLimit());
        }
    }

    /**
     * Check configuration for enabled/disabled toolbar memorizing
     *
     * @return bool
     */
    public function isMemorizingAllowed()
    {
        if ($this->isMemorizingAllowed === null) {
            $this->isMemorizingAllowed = $this->configProvider->isRememberPagination();
        }

        return $this->isMemorizingAllowed;
    }

    /**
     * Memorize parameter value for session
     *
     * @param string $param parameter name
     * @param mixed $value parameter value
     * @return $this
     */
    private function memorizeParam($param, $value)
    {
        if ($value && $this->catalogSession->getData($param) != $value) {
            $this->catalogSession->setData($param, $value);
        }

        return $this;
    }
}
