<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterfaceFactory;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;
use MageWorx\ReviewReminderBase\Model\Reminder\Source\Type;

class Save extends Reminder
{
    /**
     * Reminder factory
     *
     * @var ReminderInterfaceFactory
     */
    protected $reminderFactory;

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
     * constructor
     *
     * @param Context $context
     * @param ReminderRepositoryInterface $reminderRepository
     * @param PageFactory $resultPageFactory
     * @param ReminderInterfaceFactory $reminderFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        ReminderRepositoryInterface $reminderRepository,
        PageFactory $resultPageFactory,
        ReminderInterfaceFactory $reminderFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor
    ) {
        $this->reminderFactory     = $reminderFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->dataPersistor       = $dataPersistor;
        parent::__construct($context, $reminderRepository, $resultPageFactory);
    }

    /**
     * run the action
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var ReminderInterface $reminder */
        $reminder       = null;
        $postData       = $this->getRequest()->getPostValue();
        $data           = $this->prepareData($postData);
        $id             = !empty($data['reminder_id']) ? $data['reminder_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $reminder = $this->reminderRepository->getById((int)$id);
            } else {
                unset($data['reminder_id']);
                $reminder = $this->reminderFactory->create();
            }

            $reminder->setData($data);

            $this->reminderRepository->save($reminder);
            $this->messageManager->addSuccessMessage(__('You saved the Reminder'));
            $this->dataPersistor->clear('mageworx_reviewreminderbase_reminder');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath($this->getEditUrl($data), ['reminder_id' => $reminder->getId()]);
            } else {
                $resultRedirect->setPath('mageworx_reviewreminderbase/reminder');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('mageworx_reviewreminderbase_reminder', $postData);
            $resultRedirect->setPath($this->getEditUrl($data), ['reminder_id' => $id]);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Reminder'));
            $this->dataPersistor->set('mageworx_reviewreminderbase_reminder', $postData);
            $resultRedirect->setPath($this->getEditUrl($data), ['reminder_id' => $id]);
        }

        return $resultRedirect;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data): array
    {
        return $data;
    }

    /**
     * @param array $data
     * @return string
     * @throws Exception
     */
    protected function getEditUrl($data)
    {
        return 'mageworx_reviewreminderbase/reminder/edit' . \ucfirst($this->getReminderType($data));
    }

    /**
     * @param array $data
     * @return string
     * @throws Exception
     */
    protected function getReminderType($data)
    {
        if (!empty($data[ReminderInterface::TYPE])) {

            if ($data[ReminderInterface::TYPE] == Type::TYPE_POPUP) {
                return Type::TYPE_POPUP;
            }

            if ($data[ReminderInterface::TYPE] == Type::TYPE_EMAIL) {
                return Type::TYPE_EMAIL;
            }
        }

        throw new Exception('Unknown reminder type.');
    }
}
