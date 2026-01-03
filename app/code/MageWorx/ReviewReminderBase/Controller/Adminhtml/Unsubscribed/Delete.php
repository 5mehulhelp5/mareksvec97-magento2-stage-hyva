<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

class Delete extends Unsubscribed
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam('unsubscribed_id');
        if ($id) {
            try {
                $this->unsubscribedRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Unsubscribed has been deleted.'));
                $resultRedirect->setPath('mageworx_reviewreminderbase/*/');

                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Unsubscribed no longer exists.'));

                return $resultRedirect->setPath('mageworx_reviewreminderbase/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    'mageworx_reviewreminderbase/unsubscribed/edit',
                    ['unsubscribed_id' => $id]
                );
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Unsubscribed'));

                return $resultRedirect->setPath(
                    'mageworx_reviewreminderbase/unsubscribed/edit',
                    ['unsubscribed_id' => $id]
                );
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Unsubscribed to delete.'));
        $resultRedirect->setPath('mageworx_reviewreminderbase/*/');

        return $resultRedirect;
    }
}
