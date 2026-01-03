<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\ContainerManager;

use Magento\Sales\Model\Order;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManager;

class EmailContainerManager extends ContainerManager
{
    /**
     * @param Order $order
     * @param ReminderInterface $reminder
     * @param DataContainer $dataContainer
     * @return void
     */
    protected function convertSpecificData(
        Order $order,
        ReminderInterface $reminder,
        DataContainer $dataContainer
    ): void {
        $dataContainer->setEmailTemplateId($reminder->getEmailTemplateId());
        $dataContainer->setReminderId($reminder->getReminderId());
    }
}
