<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Api\Data;

/**
 * @api
 */
interface UnsubscribedSearchResultInterface
{
    /**
     * Get Unsubscribed Clients list.
     *
     * @return UnsubscribedInterface[]
     */
    public function getItems();

    /**
     * Set Unsubscribed Clients list.
     *
     * @param UnsubscribedInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
