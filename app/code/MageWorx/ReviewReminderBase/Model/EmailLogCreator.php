<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordInterface;
use MageWorx\ReviewReminderBase\Api\LogRecordRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\EmailDataContainer;
use Psr\Log\LoggerInterface;

class EmailLogCreator
{
    /**
     * @var LogRecordFactory
     */
    protected $logRecordFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var LogRecordRepositoryInterface
     */
    protected $logRecordRepository;

    /**
     * EmailLogCreator constructor.
     *
     * @param LogRecordFactory $logRecordFactory
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param LogRecordRepositoryInterface $logRecordRepository
     */
    public function __construct(
        LogRecordFactory $logRecordFactory,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        LogRecordRepositoryInterface $logRecordRepository
    ) {
        $this->logRecordFactory    = $logRecordFactory;
        $this->logger              = $logger;
        $this->serializer          = $serializer;
        $this->logRecordRepository = $logRecordRepository;
    }

    /**
     * @param EmailDataContainer $dataContainer
     * @param bool $result
     * @param string $message
     * @return void
     * @throws \Exception
     */
    public function createLog($dataContainer, $result, $message): void
    {
        try {
            /** @var LogRecordInterface $logRecord */
            $logRecord = $this->logRecordFactory->create();
            $logRecord->setStoreId($dataContainer->getStoreId());
            $logRecord->setEmailTemplateId($dataContainer->getFinalEmailTemplateId());
            $logRecord->setCustomerEmail($dataContainer->getCustomerEmail());
            $logRecord->setProductCount(count($dataContainer->getProductIds()));
            $logRecord->setDetails($this->serializer->serialize($dataContainer));
            $logRecord->setResult($result);
            $logRecord->setReminderId($dataContainer->getReminderId());
            $this->logRecordRepository->save($logRecord);
        } catch (LocalizedException | \InvalidArgumentException $e) {
            $error = $e->getMessage();
            $this->logger->critical($error);
        }
    }
}
