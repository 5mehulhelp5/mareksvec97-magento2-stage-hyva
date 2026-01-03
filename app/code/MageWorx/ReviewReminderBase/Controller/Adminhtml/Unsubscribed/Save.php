<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterface;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

class Save extends Unsubscribed
{
    /**
     * Unsubscribed factory
     *
     * @var UnsubscribedInterfaceFactory
     */
    protected $unsubscribedFactory;

    /**
     * Data Object Processor
     *
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Data Persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param UnsubscribedRepositoryInterface $unsubscribedRepository
     * @param PageFactory $resultPageFactory
     * @param UnsubscribedInterfaceFactory $unsubscribedFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        UnsubscribedRepositoryInterface $unsubscribedRepository,
        PageFactory $resultPageFactory,
        UnsubscribedInterfaceFactory $unsubscribedFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor
    ) {
        $this->unsubscribedFactory = $unsubscribedFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->dataPersistor       = $dataPersistor;
        parent::__construct($context, $unsubscribedRepository, $resultPageFactory);
    }

    /**
     * run the action
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var UnsubscribedInterface $unsubscribed */
        $unsubscribed   = null;
        $postData       = $this->getRequest()->getPostValue();
        $data           = $postData;
        $id             = !empty($data['unsubscribed_id']) ? $data['unsubscribed_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $unsubscribed = $this->unsubscribedRepository->getById((int)$id);
            } else {
                unset($data['unsubscribed_id']);
                $unsubscribed = $this->unsubscribedFactory->create();
            }
            $this->dataObjectHelper->populateWithArray(
                $unsubscribed,
                $data,
                UnsubscribedInterface::class
            );
            $this->unsubscribedRepository->save($unsubscribed);
            $this->messageManager->addSuccessMessage(__('You saved the Unsubscribed'));
            $this->dataPersistor->clear('mageworx_reviewreminderbase_unsubscribed');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath(
                    'mageworx_reviewreminderbase/unsubscribed/edit',
                    ['unsubscribed_id' => $unsubscribed->getId()]
                );
            } else {
                $resultRedirect->setPath('mageworx_reviewreminderbase/unsubscribed');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('mageworx_reviewreminderbase_unsubscribed', $postData);
            $resultRedirect->setPath('mageworx_reviewreminderbase/unsubscribed/edit', ['unsubscribed_id' => $id]);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Unsubscribed'));
            $this->dataPersistor->set('mageworx_reviewreminderbase_unsubscribed', $postData);
            $resultRedirect->setPath('mageworx_reviewreminderbase/unsubscribed/edit', ['unsubscribed_id' => $id]);
        }

        return $resultRedirect;
    }
}
