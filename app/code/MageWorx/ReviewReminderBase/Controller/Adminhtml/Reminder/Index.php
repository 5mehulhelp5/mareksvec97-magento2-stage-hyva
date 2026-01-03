<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Magento\Backend\Model\View\Result\Page;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;
use MageWorx\ReviewReminderBase\Observer\EmailNotificationObserver;

class Index extends Reminder
{
    /**
     * Reminders list.
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_ReviewReminderBase::reminder');
        $resultPage->getConfig()->getTitle()->prepend(__('Reminders'));
        $resultPage->addBreadcrumb(__('Review Reminder'), __('Review Reminder'));
        $resultPage->addBreadcrumb(__('Reminders'), __('Reminders'));

        return $resultPage;
    }
}
