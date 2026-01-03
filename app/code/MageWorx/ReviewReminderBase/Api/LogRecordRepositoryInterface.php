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
use MageWorx\ReviewReminderBase\Api\Data\LogRecordInterface;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordSearchResultInterface;

/**
 * @api
 */
interface LogRecordRepositoryInterface
{
    /**
     * Save LogRecord.
     *
     * @param LogRecordInterface $logRecord
     * @return LogRecordInterface
     * @throws LocalizedException
     */
    public function save(LogRecordInterface $logRecord): LogRecordInterface;

    /**
     * Retrieve LogRecord
     *
     * @param int $logRecordId
     * @return LogRecordInterface
     * @throws LocalizedException
     */
    public function getById(int $logRecordId): LogRecordInterface;

    /**
     * Retrieve Log matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return LogRecordSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): LogRecordSearchResultInterface;

    /**
     * Delete LogRecord.
     *
     * @param LogRecordInterface $logRecord
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(LogRecordInterface $logRecord): bool;

    /**
     * Delete LogRecord by ID.
     *
     * @param int $logRecordId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $logRecordId): bool;
}
