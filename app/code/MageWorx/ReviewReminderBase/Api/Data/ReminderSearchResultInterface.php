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
interface ReminderSearchResultInterface
{
    /**
     * Get Reminders list.
     *
     * @return ReminderInterface[]
     */
    public function getItems();

    /**
     * Set Reminders list.
     *
     * @param ReminderInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
