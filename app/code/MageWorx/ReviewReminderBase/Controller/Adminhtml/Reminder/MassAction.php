<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\CollectionFactory;

abstract class MassAction extends Action
{
    /**
     * Reminder repository
     *
     * @var ReminderRepositoryInterface
     */
    protected $reminderRepository;

    /**
     * Mass Action filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Reminder collection factory
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
     * @param ReminderRepositoryInterface $reminderRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        Context $context,
        ReminderRepositoryInterface $reminderRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->reminderRepository = $reminderRepository;
        $this->filter             = $filter;
        $this->collectionFactory  = $collectionFactory;
        $this->successMessage     = $successMessage;
        $this->errorMessage       = $errorMessage;
        parent::__construct($context);
    }

    /**
     * @param ReminderInterface $reminder
     * @return mixed
     */
    abstract protected function massAction(ReminderInterface $reminder);

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
            foreach ($collection as $reminder) {
                $this->massAction($reminder);
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
