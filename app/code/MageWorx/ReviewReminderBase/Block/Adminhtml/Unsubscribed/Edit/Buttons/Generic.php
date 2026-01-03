<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Block\Adminhtml\Unsubscribed\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface;

class Generic
{
    /**
     * Widget Context
     *
     * @var Context
     */
    protected $context;

    /**
     * Unsubscribed Repository
     *
     * @var UnsubscribedRepositoryInterface
     */
    protected $unsubscribedRepository;

    /**
     * constructor
     *
     * @param Context $context
     * @param UnsubscribedRepositoryInterface $unsubscribedRepository
     */
    public function __construct(
        Context $context,
        UnsubscribedRepositoryInterface $unsubscribedRepository
    ) {
        $this->context                = $context;
        $this->unsubscribedRepository = $unsubscribedRepository;
    }

    /**
     * Return Unsubscribed ID
     *
     * @return int|null
     */
    public function getUnsubscribedId()
    {
        try {
            return $this->unsubscribedRepository->getById(
                (int)$this->context->getRequest()->getParam('unsubscribed_id')
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
