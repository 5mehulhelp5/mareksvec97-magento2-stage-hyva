<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterface;
use MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed\CollectionFactory;

abstract class MassAction extends Action
{
    /**
     * Unsubscribed repository
     *
     * @var UnsubscribedRepositoryInterface
     */
    protected $unsubscribedRepository;

    /**
     * Mass Action filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Unsubscribed collection factory
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
     * @param UnsubscribedRepositoryInterface $unsubscribedRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        Context $context,
        UnsubscribedRepositoryInterface $unsubscribedRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->unsubscribedRepository = $unsubscribedRepository;
        $this->filter                 = $filter;
        $this->collectionFactory      = $collectionFactory;
        $this->successMessage         = $successMessage;
        $this->errorMessage           = $errorMessage;
        parent::__construct($context);
    }

    /**
     * @param UnsubscribedInterface $unsubscribed
     * @return mixed
     */
    abstract protected function massAction(UnsubscribedInterface $unsubscribed);

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
            foreach ($collection as $unsubscribed) {
                $this->massAction($unsubscribed);
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
