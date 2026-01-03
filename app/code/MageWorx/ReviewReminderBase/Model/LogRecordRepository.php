<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\AbstractModel;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordInterface;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordSearchResultInterface;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordSearchResultInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\LogRecordRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord\Collection;
use MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord\CollectionFactory;

class LogRecordRepository implements LogRecordRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * LogRecord resource model
     *
     * @var \MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord
     */
    protected $resource;

    /**
     * LogRecord collection factory
     *
     * @var CollectionFactory
     */
    protected $logRecordCollectionFactory;

    /**
     * LogRecord interface factory
     *
     * @var LogRecordInterfaceFactory
     */
    protected $logRecordInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var LogRecordSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     *
     * @param \MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord $resource
     * @param CollectionFactory $logRecordCollectionFactory
     * @param LogRecordInterfaceFactory $logRecordInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param LogRecordSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord $resource,
        CollectionFactory $logRecordCollectionFactory,
        LogRecordInterfaceFactory $logRecordInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        LogRecordSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource                   = $resource;
        $this->logRecordCollectionFactory = $logRecordCollectionFactory;
        $this->logRecordInterfaceFactory  = $logRecordInterfaceFactory;
        $this->dataObjectHelper           = $dataObjectHelper;
        $this->searchResultsFactory       = $searchResultsFactory;
    }

    /**
     * Save LogRecord.
     *
     * @param LogRecordInterface $logRecord
     * @return LogRecordInterface
     * @throws LocalizedException
     */
    public function save(LogRecordInterface $logRecord): LogRecordInterface
    {
        /** @var LogRecordInterface|AbstractModel $logRecord */
        try {
            $this->resource->save($logRecord);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the LogRecord: %1',
                    $exception->getMessage()
                )
            );
        }

        return $logRecord;
    }

    /**
     * Retrieve LogRecord.
     *
     * @param int $logRecordId
     * @return LogRecordInterface
     * @throws LocalizedException
     */
    public function getById(int $logRecordId): LogRecordInterface
    {
        if (!isset($this->instances[$logRecordId])) {
            /** @var LogRecordInterface|AbstractModel $logRecord */
            $logRecord = $this->logRecordInterfaceFactory->create();
            $this->resource->load($logRecord, $logRecordId);
            if (!$logRecord->getId()) {
                throw new NoSuchEntityException(__('Requested LogRecord doesn\'t exist'));
            }
            $this->instances[$logRecordId] = $logRecord;
        }

        return $this->instances[$logRecordId];
    }

    /**
     * Retrieve Log matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return LogRecordSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): LogRecordSearchResultInterface
    {
        /** @var LogRecordSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $collection */
        $collection = $this->logRecordCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            $field = 'record_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var LogRecordInterface[] $log */
        $log = [];
        /** @var LogRecord $logRecord */
        foreach ($collection as $logRecord) {
            /** @var LogRecordInterface $logRecordDataObject */
            $logRecordDataObject = $this->logRecordInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $logRecordDataObject,
                $logRecord->getData(),
                LogRecordInterface::class
            );
            $log[] = $logRecordDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults->setItems($log);
    }

    /**
     * Delete LogRecord.
     *
     * @param LogRecordInterface $logRecord
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(LogRecordInterface $logRecord): bool
    {
        /** @var LogRecordInterface|AbstractModel $logRecord */
        $id = $logRecord->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($logRecord);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new StateException(
                __('Unable to remove LogRecord %1', $id)
            );
        }
        unset($this->instances[$id]);

        return true;
    }

    /**
     * Delete LogRecord by ID.
     *
     * @param int $logRecordId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function deleteById(int $logRecordId): bool
    {
        $logRecord = $this->getById($logRecordId);

        return $this->delete($logRecord);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws InputException
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ): LogRecordRepositoryInterface {
        $fields     = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition    = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[]     = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }

        return $this;
    }
}
