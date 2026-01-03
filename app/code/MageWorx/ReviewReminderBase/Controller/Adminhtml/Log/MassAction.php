<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Log;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordInterface;
use MageWorx\ReviewReminderBase\Api\LogRecordRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord\CollectionFactory;

abstract class MassAction extends Action
{
    /**
     * LogRecord repository
     *
     * @var LogRecordRepositoryInterface
     */
    protected $logRecordRepository;

    /**
     * Mass Action filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * LogRecord collection factory
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Action success message
     *
     * @var string
     */
    protected $successMessage;

    /**
     * Action error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * constructor
     *
     * @param Context $context
     * @param LogRecordRepositoryInterface $logRecordRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        Context $context,
        LogRecordRepositoryInterface $logRecordRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->logRecordRepository = $logRecordRepository;
        $this->filter              = $filter;
        $this->collectionFactory   = $collectionFactory;
        $this->successMessage      = $successMessage;
        $this->errorMessage        = $errorMessage;
        parent::__construct($context);
    }

    /**
     * @param LogRecordInterface $logRecord
     * @return mixed
     */
    abstract protected function massAction(LogRecordInterface $logRecord);

    /**
     * execute action
     *
     * @return Redirect
     */
    public function execute()
    {
        try {
            $collection     = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $logRecord) {
                $this->massAction($logRecord);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->errorMessage);
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('mageworx_reviewreminderbase/*/index');

        return $redirectResult;
    }
}
