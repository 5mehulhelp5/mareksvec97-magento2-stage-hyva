<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

use Magento\Backend\Model\View\Result\Page;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

class Index extends Unsubscribed
{
    /**
     * Unsubscribed Clients list.
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_ReviewReminderBase::unsubscribed');
        $resultPage->getConfig()->getTitle()->prepend(__('Unsubscribed&#x20;Clients'));
        $resultPage->addBreadcrumb(__('Review Reminder'), __('Review Reminder'));
        $resultPage->addBreadcrumb(__('Unsubscribed&#x20;Clients'), __('Unsubscribed&#x20;Clients'));

        return $resultPage;
    }
}
