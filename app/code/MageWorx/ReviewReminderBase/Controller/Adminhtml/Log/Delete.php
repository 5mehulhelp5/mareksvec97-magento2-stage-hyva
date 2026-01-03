<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Log;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\LogRecord;

class Delete extends LogRecord
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam('record_id');
        if ($id) {
            try {
                $this->logRecordRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The LogRecord has been deleted.'));
                $resultRedirect->setPath('mageworx_reviewreminderbase/*/');

                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The LogRecord no longer exists.'));

                return $resultRedirect->setPath('mageworx_reviewreminderbase/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('mageworx_reviewreminderbase/log/edit', ['record_id' => $id]);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the LogRecord'));

                return $resultRedirect->setPath('mageworx_reviewreminderbase/log/edit', ['record_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a LogRecord to delete.'));
        $resultRedirect->setPath('mageworx_reviewreminderbase/*/');

        return $resultRedirect;
    }
}
