<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content;

use Exception;
use Magento\Sales\Model\Order;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;

interface ContainerManagerInterface
{
    /**
     * @return DataContainerInterface[]
     * @throws Exception
     */
    public function getFinalContainers(): array;

    /**
     * For using only from modifiers
     *
     * @return DataContainerInterface[]
     */
    public function getCurrentContainers(): array;

    /**
     * @param Order $order
     * @param ReminderInterface $reminder
     * @return void
     */
    public function composeContainerData(Order $order, ReminderInterface $reminder): void;
}
