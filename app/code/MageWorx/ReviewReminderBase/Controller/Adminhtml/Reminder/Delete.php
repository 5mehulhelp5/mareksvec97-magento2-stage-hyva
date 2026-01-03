<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

class Delete extends Reminder
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam('reminder_id');
        if ($id) {
            try {
                $this->reminderRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Reminder has been deleted.'));
                $resultRedirect->setPath('mageworx_reviewreminderbase/*/');

                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Reminder no longer exists.'));

                return $resultRedirect->setPath('mageworx_reviewreminderbase/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('mageworx_reviewreminderbase/reminder/edit', ['reminder_id' => $id]);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Reminder'));

                return $resultRedirect->setPath('mageworx_reviewreminderbase/reminder/edit', ['reminder_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Reminder to delete.'));
        $resultRedirect->setPath('mageworx_reviewreminderbase/*/');

        return $resultRedirect;
    }
}
