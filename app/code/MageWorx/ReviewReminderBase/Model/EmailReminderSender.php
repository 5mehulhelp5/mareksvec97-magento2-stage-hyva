<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use Psr\Log\LoggerInterface;

class EmailReminderSender
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var ReminderConfigReader
     */
    protected $configReader;

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * EmailReminderSender constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param ReminderConfigReader $configReader
     * @param LoggerInterface $logger
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        ReminderConfigReader $configReader,
        LoggerInterface $logger,
        ManagerInterface $eventManager
    ) {
        $this->storeManager     = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->configReader     = $configReader;
        $this->logger           = $logger;
        $this->eventManager     = $eventManager;
    }

    /**
     * @param DataContainer $emailDataContainer
     * @return bool
     * @throws LocalizedException
     */
    public function sendEmail(DataContainer $emailDataContainer): bool
    {
        $storeId    = $emailDataContainer->getStoreId();
        $templateId = $emailDataContainer->getFinalEmailTemplateId();
        $from       = $this->configReader->getEmailSender($storeId);

        $this->transportBuilder->setTemplateIdentifier($templateId);
        $this->transportBuilder->setTemplateOptions(
            [
                'store' => $storeId,
                'area'  => Area::AREA_FRONTEND
            ]
        );

        $this->transportBuilder->setTemplateVars(
            [
                'customer_name'   => $emailDataContainer->getCustomerFirstName(),
                'store'           => $this->storeManager->getStore($storeId),
                'products'        => $emailDataContainer->getOrderProducts(),
                'reviews'         => $emailDataContainer->getReviews(),
                'unsubscribe_url' => $emailDataContainer->getUnsubscribeUrl(),
            ]
        )->setFrom(
            $from
        )->addTo(
            $emailDataContainer->getCustomerEmail(),
            $emailDataContainer->getCustomerFirstName()
        );

        $transport = $this->transportBuilder->getTransport();

        try {
            $transport->sendMessage();
            $result = true;
        } catch (MailException $e) {
            $result       = false;
            $errorMessage = $e->getMessage();
            $this->logger->critical($errorMessage);
        }

        $this->eventManager->dispatch(
            'mageworx_reviewreminderbase_send_email_after',
            [
                'result' => new DataObject(
                    [
                        'result'               => $result,
                        'message'              => $errorMessage ?? null,
                        'email_data_container' => $emailDataContainer
                    ]
                )
            ]
        );

        return $result;
    }
}
