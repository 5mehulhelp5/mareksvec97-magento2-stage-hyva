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
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\Data\ReminderSearchResultInterface;
use MageWorx\ReviewReminderBase\Api\Data\ReminderSearchResultInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Collection;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\CollectionFactory;

class ReminderRepository implements ReminderRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Reminder resource model
     *
     * @var \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder
     */
    protected $resource;

    /**
     * Reminder collection factory
     *
     * @var CollectionFactory
     */
    protected $reminderCollectionFactory;

    /**
     * Reminder interface factory
     *
     * @var ReminderInterfaceFactory
     */
    protected $reminderInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var ReminderSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     *
     * @param \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder $resource
     * @param CollectionFactory $reminderCollectionFactory
     * @param ReminderInterfaceFactory $reminderInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ReminderSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder $resource,
        CollectionFactory $reminderCollectionFactory,
        ReminderInterfaceFactory $reminderInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        ReminderSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource                  = $resource;
        $this->reminderCollectionFactory = $reminderCollectionFactory;
        $this->reminderInterfaceFactory  = $reminderInterfaceFactory;
        $this->dataObjectHelper          = $dataObjectHelper;
        $this->searchResultsFactory      = $searchResultsFactory;
    }

    /**
     * Save Reminder.
     *
     * @param ReminderInterface $reminder
     * @return ReminderInterface
     * @throws LocalizedException
     */
    public function save(ReminderInterface $reminder): ReminderInterface
    {
        /** @var ReminderInterface|AbstractModel $reminder */
        try {
            $this->resource->save($reminder);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the Reminder: %1',
                    $exception->getMessage()
                )
            );
        }

        return $reminder;
    }

    /**
     * Retrieve Reminder.
     *
     * @param int $reminderId
     * @return ReminderInterface
     * @throws LocalizedException
     */
    public function getById(int $reminderId): ReminderInterface
    {
        if (!isset($this->instances[$reminderId])) {
            /** @var ReminderInterface|AbstractModel $reminder */
            $reminder = $this->reminderInterfaceFactory->create();
            $this->resource->load($reminder, $reminderId);
            if (!$reminder->getId()) {
                throw new NoSuchEntityException(__('Requested Reminder doesn\'t exist'));
            }
            $this->instances[$reminderId] = $reminder;
        }

        return $this->instances[$reminderId];
    }

    /**
     * Retrieve Reminders matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ReminderSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ReminderSearchResultInterface
    {
        /** @var ReminderSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $collection */
        $collection = $this->reminderCollectionFactory->create();

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
            $field = 'reminder_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var ReminderInterface[] $reminders */
        $reminders = [];
        /** @var Reminder $reminder */
        foreach ($collection as $reminder) {
            /** @var ReminderInterface $reminderDataObject */
            $reminderDataObject = $this->reminderInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $reminderDataObject,
                $reminder->getData(),
                ReminderInterface::class
            );
            $reminders[] = $reminderDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults->setItems($reminders);
    }

    /**
     * Delete Reminder.
     *
     * @param ReminderInterface $reminder
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ReminderInterface $reminder): bool
    {
        /** @var ReminderInterface|AbstractModel $reminder */
        $id = $reminder->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($reminder);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new StateException(
                __('Unable to remove Reminder %1', $id)
            );
        }
        unset($this->instances[$id]);

        return true;
    }

    /**
     * Delete Reminder by ID.
     *
     * @param int $reminderId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function deleteById(int $reminderId): bool
    {
        $reminder = $this->getById($reminderId);

        return $this->delete($reminder);
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
    ): ReminderRepositoryInterface {
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
