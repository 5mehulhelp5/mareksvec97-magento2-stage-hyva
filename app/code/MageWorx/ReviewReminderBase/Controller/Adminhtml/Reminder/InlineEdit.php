<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\Reminder;
use RuntimeException;

class InlineEdit extends \MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder
{
    /**
     * Data object processor
     *
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Data object helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * Reminder resource model
     *
     * @var \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder
     */
    protected $reminderResourceModel;

    /**
     * constructor
     *
     * @param Context $context
     * @param ReminderRepositoryInterface $reminderRepository
     * @param PageFactory $resultPageFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder $reminderResourceModel
     */
    public function __construct(
        Context $context,
        ReminderRepositoryInterface $reminderRepository,
        PageFactory $resultPageFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder $reminderResourceModel
    ) {
        $this->dataObjectProcessor   = $dataObjectProcessor;
        $this->dataObjectHelper      = $dataObjectHelper;
        $this->jsonFactory           = $jsonFactory;
        $this->reminderResourceModel = $reminderResourceModel;
        parent::__construct($context, $reminderRepository, $resultPageFactory);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error      = false;
        $messages   = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                    'messages' => [__('Please correct the data sent.')],
                    'error'    => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $reminderId) {
            /** @var Reminder|ReminderInterface $reminder */
            $reminder = $this->reminderRepository->getById((int)$reminderId);
            try {
                $reminderData = $postItems[$reminderId];
                $this->dataObjectHelper->populateWithArray(
                    $reminder,
                    $reminderData,
                    ReminderInterface::class
                );
                $this->reminderResourceModel->saveAttribute($reminder, array_keys($reminderData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithReminderId($reminder, $e->getMessage());
                $error      = true;
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithReminderId($reminder, $e->getMessage());
                $error      = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithReminderId(
                    $reminder,
                    __('Something went wrong while saving the Reminder.')
                );
                $error      = true;
            }
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error'    => $error
            ]
        );
    }

    /**
     * Add Reminder id to error message
     *
     * @param ReminderInterface $reminder
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithReminderId(
        ReminderInterface $reminder,
        $errorText
    ) {
        return '[Reminder ID: ' . $reminder->getId() . '] ' . $errorText;
    }
}
