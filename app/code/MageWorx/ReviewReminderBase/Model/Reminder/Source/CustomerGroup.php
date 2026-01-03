<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Reminder\Source;

use Magento\Customer\Api\Data\GroupSearchResultsInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\Manager as ModuleManager;
use MageWorx\ReviewReminderBase\Model\Source;

class CustomerGroup extends Source
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * DisplayMode constructor.
     *
     * @param ModuleManager $moduleManager
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ModuleManager $moduleManager,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->moduleManager         = $moduleManager;
        $this->groupRepository       = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Return array of customer groups
     *
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }

        $customerGroups = [];

        /** @var GroupSearchResultsInterface $groups */
        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($groups->getItems() as $group) {
            $customerGroups[] = [
                'label' => $group->getCode(),
                'value' => $group->getId(),
            ];
        }

        return $customerGroups;
    }
}
