<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ReviewReminderBase\Model\EmailLogCreator;

class LogEmailObserver implements ObserverInterface
{
    /**
     * @var EmailLogCreator
     */
    protected $emailLogCreator;

    /**
     * EmailLogObserver constructor.
     *
     * @param EmailLogCreator $emailLogCreator
     */
    public function __construct(
        EmailLogCreator $emailLogCreator
    ) {
        $this->emailLogCreator = $emailLogCreator;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $result  = $observer->getResult()->getResult();
        $message = $observer->getResult()->getMessage();

        /** @var \MageWorx\ReviewReminderBase\Model\Content\DataContainer $emailDataContainer */
        $emailDataContainer = $observer->getResult()->getEmailDataContainer();

        $this->emailLogCreator->createLog($emailDataContainer, $result, $message);
    }
}
