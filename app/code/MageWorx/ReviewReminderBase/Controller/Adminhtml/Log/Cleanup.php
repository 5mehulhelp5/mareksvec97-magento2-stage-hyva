<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Log;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Model\ReminderConfigReader;
use MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord as LogRecordResource;
use Psr\Log\LoggerInterface;

class Cleanup extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var LogRecordResource
     */
    protected $logRecordResource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Cleanup constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param LogRecordResource $logRecordResource
     * @param ReminderConfigReader $reminderConfigReader
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LogRecordResource $logRecordResource,
        ReminderConfigReader $reminderConfigReader,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory    = $resultPageFactory;
        $this->logRecordResource    = $logRecordResource;
        $this->reminderConfigReader = $reminderConfigReader;
        $this->logger               = $logger;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $numOfDays   = $this->reminderConfigReader->getUntouchablePeriodForCleanup();
            $deleteCount = $this->logRecordResource->clean($numOfDays);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/');
        }

        $this->logger->info(__('MageWorx Review Reminder: Removed %1 records from log.', $deleteCount));

        $this->messageManager->addSuccessMessage(
            __('Removed %1 records from %2 days ago or older', $deleteCount, $numOfDays)
        );

        return $resultRedirect->setPath('*/*/');
    }
}
