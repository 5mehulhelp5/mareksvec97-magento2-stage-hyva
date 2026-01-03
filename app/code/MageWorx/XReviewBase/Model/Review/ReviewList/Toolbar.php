<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Model\Review\ReviewList;

/**
 * Class Toolbar
 */
class Toolbar
{
    /**
     * GET parameter page variable name
     */
    const PAGE_PARM_NAME = 'p';

    /**
     * Sort order cookie name
     */
    const ORDER_PARAM_NAME = 'review_list_order';

    /**
     * Sort direction cookie name
     */
    const DIRECTION_PARAM_NAME = 'review_list_dir';

    /**
     * Filter by verified buyer cookie name
     */
    const FILTER_PARAM_NAME = 'review_list_filter';

    /**
     * Filter by media cookie name
     */
    const FILTER_MEDIA_PARAM_NAME = 'review_list_filter_media';

    /**
     * Filter by location cookie name
     */
    const FILTER_LOCATION_PARAM_NAME = 'review_list_filter_location';

    /**
     * Review per page limit order cookie name
     */
    const LIMIT_PARAM_NAME = 'review_list_limit';

    /**
     * Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    /**
     * Get sort order
     *
     * @return string|bool
     */
    public function getOrder()
    {
        return $this->request->getParam(self::ORDER_PARAM_NAME);
    }

    /**
     * Get sort direction
     *
     * @return string|bool
     */
    public function getDirection()
    {
        return $this->request->getParam(self::DIRECTION_PARAM_NAME);
    }

    /**
     * Get versified buyers filter
     *
     * @return string|bool
     */
    public function getFilter()
    {
        return $this->request->getParam(self::FILTER_PARAM_NAME);
    }

    /**
     * Get media filter
     *
     * @return string|bool
     */
    public function getMediaFilter()
    {
        return $this->request->getParam(self::FILTER_MEDIA_PARAM_NAME);
    }

    /**
     * Get location filter
     *
     * @return string|bool
     */
    public function getLocationFilter()
    {
        return $this->request->getParam(self::FILTER_LOCATION_PARAM_NAME);
    }

    /**
     * Get review per page limit
     *
     * @return string|bool
     */
    public function getLimit()
    {
        return $this->request->getParam(self::LIMIT_PARAM_NAME);
    }

    /**
     * Return current page from request
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $page = (int)$this->request->getParam(self::PAGE_PARM_NAME);

        return $page ?: 1;
    }
}
