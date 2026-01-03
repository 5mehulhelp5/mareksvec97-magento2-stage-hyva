<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Model\AbstractModel;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterface;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedSearchResultInterface;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedSearchResultInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed\Collection;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed\CollectionFactory;

class UnsubscribedRepository implements UnsubscribedRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Unsubscribed resource model
     *
     * @var \MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed
     */
    protected $resource;

    /**
     * Unsubscribed collection factory
     *
     * @var CollectionFactory
     */
    protected $unsubscribedCollectionFactory;

    /**
     * Unsubscribed interface factory
     *
     * @var UnsubscribedInterfaceFactory
     */
    protected $unsubscribedInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var UnsubscribedSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     *
     * @param \MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed $resource
     * @param CollectionFactory $unsubscribedCollectionFactory
     * @param UnsubscribedInterfaceFactory $unsubscribedInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param UnsubscribedSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed $resource,
        CollectionFactory $unsubscribedCollectionFactory,
        UnsubscribedInterfaceFactory $unsubscribedInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        UnsubscribedSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource                      = $resource;
        $this->unsubscribedCollectionFactory = $unsubscribedCollectionFactory;
        $this->unsubscribedInterfaceFactory  = $unsubscribedInterfaceFactory;
        $this->dataObjectHelper              = $dataObjectHelper;
        $this->searchResultsFactory          = $searchResultsFactory;
    }

    /**
     * Save Unsubscribed.
     *
     * @param UnsubscribedInterface $unsubscribed
     * @return UnsubscribedInterface
     * @throws LocalizedException
     */
    public function save(UnsubscribedInterface $unsubscribed): UnsubscribedInterface
    {
        /** @var UnsubscribedInterface|AbstractModel $unsubscribed */
        try {
            $this->resource->save($unsubscribed);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the Unsubscribed: %1',
                    $exception->getMessage()
                )
            );
        }

        return $unsubscribed;
    }

    /**
     * Retrieve Unsubscribed.
     *
     * @param int $unsubscribedId
     * @return UnsubscribedInterface
     * @throws LocalizedException
     */
    public function getById(int $unsubscribedId): UnsubscribedInterface
    {
        if (!isset($this->instances[$unsubscribedId])) {
            /** @var UnsubscribedInterface|AbstractModel $unsubscribed */
            $unsubscribed = $this->unsubscribedInterfaceFactory->create();
            $this->resource->load($unsubscribed, $unsubscribedId);
            if (!$unsubscribed->getId()) {
                throw new NoSuchEntityException(
                    __('Requested Unsubscribed doesn\'t exist')
                );
            }
            $this->instances[$unsubscribedId] = $unsubscribed;
        }

        return $this->instances[$unsubscribedId];
    }

    /**
     * @param string $unsubscribedEmail
     * @return UnsubscribedInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getByEmail(string $unsubscribedEmail): UnsubscribedInterface
    {
        if (!isset($this->instances[$unsubscribedEmail])) {
            /** @var UnsubscribedInterface|AbstractModel $unsubscribed */
            $unsubscribed = $this->unsubscribedInterfaceFactory->create();
            $this->resource->load($unsubscribed, $unsubscribedEmail, 'email');
            if (!$unsubscribed->getId()) {
                throw new NoSuchEntityException(
                    __('Requested Unsubscribed doesn\'t exist')
                );
            }
            $this->instances[$unsubscribedEmail] = $unsubscribed;
        }

        return $this->instances[$unsubscribedEmail];
    }

    /**
     * Retrieve Unsubscribed Clients matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var UnsubscribedSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $collection */
        $collection = $this->unsubscribedCollectionFactory->create();

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
            $field = 'unsubscribed_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var UnsubscribedInterface[] $unsubscribedClients */
        $unsubscribedClients = [];
        /** @var Unsubscribed $unsubscribed */
        foreach ($collection as $unsubscribed) {
            /** @var UnsubscribedInterface $unsubscribedDataObject */
            $unsubscribedDataObject = $this->unsubscribedInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $unsubscribedDataObject,
                $unsubscribed->getData(),
                UnsubscribedInterface::class
            );
            $unsubscribedClients[] = $unsubscribedDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults->setItems($unsubscribedClients);
    }

    /**
     * Delete Unsubscribed.
     *
     * @param UnsubscribedInterface $unsubscribed
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(UnsubscribedInterface $unsubscribed): bool
    {
        /** @var UnsubscribedInterface|AbstractModel $unsubscribed */
        $id = $unsubscribed->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($unsubscribed);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new StateException(
                __('Unable to remove Unsubscribed %1', $id)
            );
        }
        unset($this->instances[$id]);

        return true;
    }

    /**
     * Delete Unsubscribed by ID.
     *
     * @param int $unsubscribedId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function deleteById(int $unsubscribedId): bool
    {
        $unsubscribed = $this->getById($unsubscribedId);

        return $this->delete($unsubscribed);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return UnsubscribedRepositoryInterface
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ): UnsubscribedRepositoryInterface {
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
