<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Block\Adminhtml\Reminder\Grid\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Cleanup implements ButtonProviderInterface
{
    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \MageWorx\ReviewReminderBase\Model\ReminderConfigReader
     */
    protected $reminderConfigReader;

    /**
     * Cleanup constructor.
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \MageWorx\ReviewReminderBase\Model\ReminderConfigReader $reminderConfigReader
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorx\ReviewReminderBase\Model\ReminderConfigReader $reminderConfigReader
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->reminderConfigReader = $reminderConfigReader;
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label'      => __('Run Cleanup'),
            'class'      => 'delete',
            'on_click'   => 'deleteConfirm(\'' . __(
                    'The records for the reminders sent more than %1 days ago will be removed. Are you sure you want to proceed?',
                    $this->reminderConfigReader->getUntouchablePeriodForCleanup()
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
            'sort_order' => 20,
        ];

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/cleanup');
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
        return $this->urlBuilder->getUrl($route, $params);
    }
}
