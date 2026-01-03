<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Magento\Backend\Model\View\Result\Page;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

class EditPopup extends Reminder
{
    /**
     * @return int
     */
    protected function initReminder()
    {
        return $this->getRequest()->getParam('reminder_id');
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
            $resultPage->addBreadcrumb(__('New Popup Reminder'), __('New Popup Reminder'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Popup Reminder'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Popup Reminder'), __('Edit Popup Reminder'));
            $resultPage->getConfig()->getTitle()->prepend(
                __(
                    'Popup reminder: %1',
                    $this->reminderRepository->getById($reminderId)->getName()
                )
            );
        }

        return $resultPage;
    }
}
