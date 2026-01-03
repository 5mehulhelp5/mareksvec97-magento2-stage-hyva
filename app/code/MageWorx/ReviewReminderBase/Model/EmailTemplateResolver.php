<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Exception;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\EmailDataContainer;

class EmailTemplateResolver
{
    /**
     * @var ReminderConfigReader
     */
    protected $reminderConfigReader;

    /**
     * EmailTemplateResolver constructor.
     *
     * @param ReminderConfigReader $reminderConfigReader
     */
    public function __construct(
        ReminderConfigReader $reminderConfigReader
    ) {
        $this->reminderConfigReader = $reminderConfigReader;
    }

    /**
     * @param EmailDataContainer $emailDataContainer
     * @return string
     * @throws Exception
     */
    public function resolveTemplate(EmailDataContainer $emailDataContainer): string
    {
        $storeId = $emailDataContainer->getStoreId();

        if (!(int)$storeId) {
            throw new Exception('Email container must be initialized.');
        }

        $templateId = $emailDataContainer->getEmailTemplateId();

        if (!$templateId) {
            $templateId = $this->reminderConfigReader->getNotificationEmailTemplateId($storeId);
        }

        return $templateId;
    }
}
