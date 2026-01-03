<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Source;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;

class Reminder implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Reminder repository
     *
     * @var ReminderRepositoryInterface
     */
    protected $reminderRepository;

    /**
     * Search Criteria Builder
     *
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Filter Builder
     *
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * constructor
     *
     * @param ReminderRepositoryInterface $reminderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        ReminderRepositoryInterface $reminderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->reminderRepository    = $reminderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder         = $filterBuilder;
    }

    /**
     * Retrieve all Reminders as an option array
     *
     * @return array
     * @throws StateException
     */
    public function getAllOptions()
    {
        if (empty($this->options)) {
            $options        = [];
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults  = $this->reminderRepository->getList($searchCriteria);
            foreach ($searchResults->getItems() as $reminder) {
                $options[] = [
                    'value' => $reminder->getReminderId(),
                    'label' => $reminder->getName(),
                ];
            }
            $this->options = $options;
        }

        return $this->options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
