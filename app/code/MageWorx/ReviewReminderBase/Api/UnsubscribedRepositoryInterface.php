<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterface;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedSearchResultInterface;

/**
 * @api
 */
interface UnsubscribedRepositoryInterface
{
    /**
     * Save Unsubscribed.
     *
     * @param UnsubscribedInterface $unsubscribed
     * @return UnsubscribedInterface
     * @throws LocalizedException
     */
    public function save(UnsubscribedInterface $unsubscribed): UnsubscribedInterface;

    /**
     * Retrieve Unsubscribed
     *
     * @param int $unsubscribedId
     * @return UnsubscribedInterface
     * @throws LocalizedException
     */
    public function getById(int $unsubscribedId): UnsubscribedInterface;

    /**
     * Retrieve Unsubscribed
     *
     * @param string $unsubscribedEmail
     * @return UnsubscribedInterface
     * @throws NoSuchEntityException
     */
    public function getByEmail(string $unsubscribedEmail): UnsubscribedInterface;

    /**
     * Retrieve Unsubscribed Clients matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Unsubscribed.
     *
     * @param UnsubscribedInterface $unsubscribed
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(UnsubscribedInterface $unsubscribed): bool;

    /**
     * Delete Unsubscribed by ID.
     *
     * @param int $unsubscribedId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $unsubscribedId): bool;
}
