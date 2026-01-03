<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;

abstract class Reminder extends Action
{
    /**
     * Reminder repository
     *
     * @var ReminderRepositoryInterface
     */
    protected $reminderRepository;

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
     * @param ReminderRepositoryInterface $reminderRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ReminderRepositoryInterface $reminderRepository,
        PageFactory $resultPageFactory
    ) {
        $this->reminderRepository = $reminderRepository;
        $this->resultPageFactory  = $resultPageFactory;
        parent::__construct($context);
    }
}
