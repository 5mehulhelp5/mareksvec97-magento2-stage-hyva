<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Magento\Backend\Model\View\Result\Page;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

class EditEmail extends Reminder
{
    /**
     * @return int
     */
    protected function initReminder()
    {
        return (int)$this->getRequest()->getParam('reminder_id');
    }

    /**
     * Edit or create Reminder
     *
     * @return Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $reminderId = $this->initReminder();

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_ReviewReminderBase::reviewreminderbase_reminder');
        $resultPage->getConfig()->getTitle()->prepend(__('Reminders'));
        $resultPage->addBreadcrumb(__('Review Reminder'), __('Review Reminder'));
        $resultPage->addBreadcrumb(
            __('Reminders'),
            __('Reminders'),
            $this->getUrl('mageworx_reviewreminderbase/reminder')
        );

        if ($reminderId === null) {
            $resultPage->addBreadcrumb(__('New Email Reminder'), __('New Email Reminder'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Email Reminder'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Email Reminder'), __('Edit Email Reminder'));
            $resultPage->getConfig()->getTitle()->prepend(
                __(
                    'Email reminder: %1',
                    $this->reminderRepository->getById($reminderId)->getName()
                )
            );
        }

        return $resultPage;
    }
}
