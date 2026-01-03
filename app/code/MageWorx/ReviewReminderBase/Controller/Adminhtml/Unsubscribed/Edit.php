<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

use Magento\Backend\Model\View\Result\Page;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

class Edit extends Unsubscribed
{
    /**
     * @return int
     */
    protected function initUnsubscribed()
    {
        return $this->getRequest()->getParam('unsubscribed_id');
    }

    /**
     * Edit or create Unsubscribed
     *
     * @return Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $unsubscribedId = $this->initUnsubscribed();

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageWorx_ReviewReminderBase::reviewreminderbase_unsubscribed');
        $resultPage->getConfig()->getTitle()->prepend(__('Unsubscribed&#x20;Clients'));
        $resultPage->addBreadcrumb(__('Review Reminder'), __('Review Reminder'));
        $resultPage->addBreadcrumb(
            __('Unsubscribed&#x20;Clients'),
            __('Unsubscribed&#x20;Clients'),
            $this->getUrl('mageworx_reviewreminderbase/unsubscribed')
        );

        if ($unsubscribedId === null) {
            $resultPage->addBreadcrumb(__('New Unsubscribed'), __('New Unsubscribed'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Unsubscribed'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Unsubscribed'), __('Edit Unsubscribed'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->unsubscribedRepository->getById((int)$unsubscribedId)->getEmail()
            );
        }

        return $resultPage;
    }
}
