<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\Grid;

use Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface;

class Collection extends \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\Collection
    implements \Magento\Framework\Api\Search\SearchResultInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = CategoryTemplateManagementInterface::ENTITY_TYPE_CATEGORY_TEMPLATE . '_grid_collection';

    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface
     */
    protected $aggregations;

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        parent::_construct();

        $this->setModel(\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class);
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @inheritDoc
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @inheritDoc
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }
}
