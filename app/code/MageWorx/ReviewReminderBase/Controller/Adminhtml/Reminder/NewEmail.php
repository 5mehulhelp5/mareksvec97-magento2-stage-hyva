<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Magento\Backend\Model\View\Result\Page;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

class NewEmail extends Reminder
{
    /**
     * Edit or create Reminder
     *
     * @return Page
     */
    public function execute()
    {
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

        $resultPage->addBreadcrumb(__('New Email Reminder'), __('New Email Reminder'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Email Reminder'));

        return $resultPage;
    }
}
