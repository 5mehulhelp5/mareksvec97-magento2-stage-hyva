<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;
use MageWorx\ReviewReminderBase\Controller\Adminhtml\Reminder;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Template\Collection as TemplateCollection;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Template\CollectionFactory as TemplateCollectionFactory;
use MageWorx\ReviewReminderBase\Model\Reminder\Template as ReminderTemplate;

class GetTemplateHtml extends Reminder
{
    /**
     * @var TemplateCollectionFactory
     */
    protected $templateCollectionFactory;

    /**
     * GetTemplateHtml constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ReminderRepositoryInterface $reminderRepository
     * @param TemplateCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ReminderRepositoryInterface $reminderRepository,
        TemplateCollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $reminderRepository, $resultPageFactory);

        $this->templateCollectionFactory = $collectionFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $identifier = $this->getRequest()->getParam(ReminderTemplate::IDENTIFIER);
        $data       = [];

        /** @var TemplateCollection $templateCollection */
        $templateCollection = $this->templateCollectionFactory->create();

        $templateCollection
            ->addFieldToSelect(ReminderTemplate::CONTENT)
            ->addFieldToFilter(ReminderTemplate::IDENTIFIER, [$identifier]);

        $collectionData = $templateCollection->getData();

        if (empty($collectionData)) {
            $data['error'] = ['This template is missing!'];
        } else {
            $data['content'] = $collectionData[0][ReminderTemplate::CONTENT];
        }

        $resultJson->setData($data);

        return $resultJson;
    }
}
