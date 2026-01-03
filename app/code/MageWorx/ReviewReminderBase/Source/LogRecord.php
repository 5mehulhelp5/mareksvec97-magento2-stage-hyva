<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Source;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\ReviewReminderBase\Api\LogRecordRepositoryInterface;

class LogRecord implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * LogRecord repository
     *
     * @var LogRecordRepositoryInterface
     */
    protected $logRecordRepository;

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
     * @param LogRecordRepositoryInterface $logRecordRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        LogRecordRepositoryInterface $logRecordRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->logRecordRepository   = $logRecordRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder         = $filterBuilder;
    }

    /**
     * Retrieve all Log as an option array
     *
     * @return array
     * @throws StateException
     */
    public function getAllOptions()
    {
        if (empty($this->options)) {
            $options        = [];
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults  = $this->logRecordRepository->getList($searchCriteria);
            foreach ($searchResults->getItems() as $logRecord) {
                $options[] = [
                    'value' => $logRecord->getLogRecordId(),
                    'label' => $logRecord->getCustomer_email(),
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
