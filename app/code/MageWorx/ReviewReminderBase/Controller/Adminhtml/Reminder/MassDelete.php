<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;

class MassDelete extends MassAction
{
    /**
     * @param ReminderInterface $reminder
     * @return $this
     */
    protected function massAction(ReminderInterface $reminder)
    {
        $this->reminderRepository->delete($reminder);

        return $this;
    }
}
