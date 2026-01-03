<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Block\Adminhtml\Reminder\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewReminderBase\Api\ReminderRepositoryInterface;

class Generic
{
    /**
     * Widget Context
     *
     * @var Context
     */
    protected $context;

    /**
     * Reminder Repository
     *
     * @var ReminderRepositoryInterface
     */
    protected $reminderRepository;

    /**
     * constructor
     *
     * @param Context $context
     * @param ReminderRepositoryInterface $reminderRepository
     */
    public function __construct(
        Context $context,
        ReminderRepositoryInterface $reminderRepository
    ) {
        $this->context            = $context;
        $this->reminderRepository = $reminderRepository;
    }

    /**
     * Return Reminder ID
     *
     * @return int|null
     */
    public function getReminderId()
    {
        try {
            return $this->reminderRepository->getById(
                (int)$this->context->getRequest()->getParam('reminder_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
