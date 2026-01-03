<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord\Grid;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use Psr\Log\LoggerInterface;

class Collection extends \MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord\Collection implements SearchResultInterface
{
    /**
     * Aggregations
     *
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * constructor
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     * @param string $model
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        $model = Document::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * @param string $model
     * @param string $resourceModel
     * @return Collection
     */
    public function _init($model, $resourceModel)
    {
        $this->addFilterToMap('email_template_id', 'main_table.email_template_id');

        return parent::_init($model, $resourceModel);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations): Collection
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null): Grid
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount): Grid
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null): Grid
    {
        return $this;
    }

    /**
     * Join relation table
     *
     * @param string $tableName
     * @param string $tableAlias
     * @param string $field
     * @param string $linkField
     * @return void
     */
    protected function joinRelationTable(
        $tableName,
        $tableAlias,
        $field,
        $linkField = ReminderInterface::REMINDER_ID
    ): void {
        if (!in_array($tableName, $this->_joinedTables)) {
            $this->getSelect()
                 ->joinLeft(
                     [$tableAlias => $this->getTable($tableName)],
                     'main_table.' . $linkField . ' = ' . $tableAlias . '.' . $linkField,
                     [$field]
                 );

            $this->_joinedTables[] = $tableName;
        }
    }

    /**
     * @return AbstractCollection
     */
    protected function _afterLoad(): AbstractCollection
    {
        $this->joinRelationTable(
            'mageworx_reviewreminderbase_reminder',
            'reminder_table',
            'name'
        );

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $this->joinRelationTable(
            'mageworx_reviewreminderbase_reminder',
            'reminder_table',
            'name'
        );

        parent::_renderFiltersBefore();
    }
}
