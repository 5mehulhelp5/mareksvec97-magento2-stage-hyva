<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Api\LogRecordRepositoryInterface;

abstract class LogRecord extends Action
{
    /**
     * LogRecord repository
     *
     * @var LogRecordRepositoryInterface
     */
    protected $logRecordRepository;

    /**
     * Page factory
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * constructor
     *
     * @param Context $context
     * @param LogRecordRepositoryInterface $logRecordRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        LogRecordRepositoryInterface $logRecordRepository,
        PageFactory $resultPageFactory
    ) {
        $this->logRecordRepository = $logRecordRepository;
        $this->resultPageFactory   = $resultPageFactory;
        parent::__construct($context);
    }
}
