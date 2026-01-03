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
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Api\Data\ReminderSearchResultInterface;

/**
 * @api
 */
interface ReminderRepositoryInterface
{
    /**
     * Save Reminder.
     *
     * @param ReminderInterface $reminder
     * @return ReminderInterface
     * @throws LocalizedException
     */
    public function save(ReminderInterface $reminder): ReminderInterface;

    /**
     * Retrieve Reminder
     *
     * @param int $reminderId
     * @return ReminderInterface
     * @throws LocalizedException
     */
    public function getById(int $reminderId): ReminderInterface;

    /**
     * Retrieve Reminders matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ReminderSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ReminderSearchResultInterface;

    /**
     * Delete Reminder.
     *
     * @param ReminderInterface $reminder
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ReminderInterface $reminder): bool;

    /**
     * Delete Reminder by ID.
     *
     * @param int $reminderId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $reminderId): bool;
}
