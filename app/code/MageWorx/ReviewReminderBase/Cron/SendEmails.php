<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Cron;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\ReviewReminderBase\Model\Content\DataProvider\EmailDataProvider;
use MageWorx\ReviewReminderBase\Model\EmailReminderSender;

class SendEmails
{
    /**
     * @var EmailDataProvider
     */
    protected $emailDataProvider;

    /**
     * @var EmailReminderSender
     */
    protected $emailReminderSender;

    /**
     * EmailNotificationObserver constructor.
     *
     * @param EmailDataProvider $emailDataProvider
     * @param EmailReminderSender $emailReminderSender
     */
    public function __construct(
        EmailDataProvider $emailDataProvider,
        EmailReminderSender $emailReminderSender
    ) {
        $this->emailDataProvider   = $emailDataProvider;
        $this->emailReminderSender = $emailReminderSender;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function execute(): SendEmails
    {
        foreach ($this->emailDataProvider->getRemindersData() as $emailDataContainer) {
            $this->emailReminderSender->sendEmail($emailDataContainer);
        }

        return $this;
    }
}
