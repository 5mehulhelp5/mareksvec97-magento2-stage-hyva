<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Api\Data;

/**
 * @api
 */
interface LogRecordSearchResultInterface
{
    /**
     * Get Log list.
     *
     * @return LogRecordInterface[]
     */
    public function getItems();

    /**
     * Set Log list.
     *
     * @param LogRecordInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
