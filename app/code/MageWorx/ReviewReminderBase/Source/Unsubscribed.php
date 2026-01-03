<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Source;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface;

class Unsubscribed implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Unsubscribed repository
     *
     * @var UnsubscribedRepositoryInterface
     */
    protected $unsubscribedRepository;

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
     * @param UnsubscribedRepositoryInterface $unsubscribedRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        UnsubscribedRepositoryInterface $unsubscribedRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->unsubscribedRepository = $unsubscribedRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->filterBuilder          = $filterBuilder;
    }

    /**
     * Retrieve all Unsubscribed Clients as an option array
     *
     * @return array
     * @throws StateException
     */
    public function getAllOptions()
    {
        if (empty($this->options)) {
            $options        = [];
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults  = $this->unsubscribedRepository->getList($searchCriteria);
            foreach ($searchResults->getItems() as $unsubscribed) {
                $options[] = [
                    'value' => $unsubscribed->getUnsubscribedId(),
                    'label' => $unsubscribed->getEmail(),
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
