<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface;

abstract class Unsubscribed extends Action
{
    /**
     * Unsubscribed repository
     *
     * @var UnsubscribedRepositoryInterface
     */
    protected $unsubscribedRepository;

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
     * @param UnsubscribedRepositoryInterface $unsubscribedRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        UnsubscribedRepositoryInterface $unsubscribedRepository,
        PageFactory $resultPageFactory
    ) {
        $this->unsubscribedRepository = $unsubscribedRepository;
        $this->resultPageFactory      = $resultPageFactory;
        parent::__construct($context);
    }
}
