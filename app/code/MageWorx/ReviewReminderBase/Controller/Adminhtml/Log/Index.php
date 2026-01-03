<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Log;

use Magento\Backend\Model\View\Result\Page;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\LogRecord;

class Index extends LogRecord
{
    /**
     * Log list.
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_ReviewReminderBase::logRecord');
        $resultPage->getConfig()->getTitle()->prepend(__('Log'));
        $resultPage->addBreadcrumb(__('Review Reminder'), __('Review Reminder'));
        $resultPage->addBreadcrumb(__('Log'), __('Log'));

        return $resultPage;
    }
}
