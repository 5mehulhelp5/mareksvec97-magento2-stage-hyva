<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Block\Reminder;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\ReviewReminderBase\Model\ReminderConfigReader;

class Popup extends Template
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Campaign config reader
     *
     * @var ReminderConfigReader
     */
    private $configReader;

    /**
     * Campaign constructor.
     *
     * @param Context $context
     * @param ReminderConfigReader $configReader
     * @param Serializer $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        ReminderConfigReader $configReader,
        Serializer $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configReader = $configReader;
        $this->serializer   = $serializer;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isCanBeDisplayed($storeId = null): bool
    {
        return $this->configReader->isPopupRemindersEnabled($storeId);
    }

    /**
     * @return string
     */
    public function getJsonConfig(): string
    {
        return $this->serializer->serialize($this->getConfig());
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'ajaxUrl' => $this->getAjaxUrl(),
        ];
    }

    /**
     * @return string
     */
    protected function getAjaxUrl(): string
    {
        return $this->getUrl('reviewreminder/ajax/getReminderData');
    }
}
