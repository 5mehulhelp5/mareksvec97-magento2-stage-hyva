<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordInterface;
use MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface;

class LogRecord extends AbstractExtensibleModel implements LogRecordInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_reviewreminderbase_logRecord';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_reviewreminderbase_logRecord';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'logRecord';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\LogRecord::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get LogRecord id
     *
     * @return array
     */
    public function getLogRecordId(): ?int
    {
        return $this->getData(LogRecordInterface::LOGRECORD_ID) === null ?
            null :
            (int)$this->getData(LogRecordInterface::LOGRECORD_ID);
    }

    /**
     * Set LogRecord id
     *
     * @param int|null $logRecordId
     * @return LogRecordInterface
     */
    public function setLogRecordId(?int $logRecordId): LogRecordInterface
    {
        return $this->setData(LogRecordInterface::LOGRECORD_ID, $logRecordId);
    }

    /**
     * Set Customer Email
     *
     * @param string|null $customerEmail
     * @return LogRecordInterface
     */
    public function setCustomerEmail(?string $customerEmail): LogRecordInterface
    {
        return $this->setData(LogRecordInterface::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Get Customer Email
     *
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->getData(LogRecordInterface::CUSTOMER_EMAIL);
    }

    /**
     * Set Email Template
     *
     * @param mixed $emailTemplateId
     * @return LogRecordInterface
     */
    public function setEmailTemplateId(?string $emailTemplateId): LogRecordInterface
    {
        return $this->setData(
            LogRecordInterface::EMAIL_TEMPLATE_ID,
            $emailTemplateId
        );
    }

    /**
     * Get Email Template
     *
     * @return string|null
     */
    public function getEmailTemplateId(): ?string
    {
        return $this->getData(LogRecordInterface::EMAIL_TEMPLATE_ID);
    }

    /**
     * Set Count Of Products
     *
     * @param int|null $productCount
     * @return LogRecordInterface
     */
    public function setProductCount(?int $productCount): LogRecordInterface
    {
        return $this->setData(LogRecordInterface::PRODUCT_COUNT, $productCount);
    }

    /**
     * Get Count Of Products
     *
     * @return int|null
     */
    public function getProductCount(): ?int
    {
        return $this->getData(LogRecordInterface::PRODUCT_COUNT) === null ?
            null :
            (int)$this->getData(LogRecordInterface::PRODUCT_COUNT);
    }

    /**
     * Set Details
     *
     * @param string|null $details
     * @return LogRecordInterface
     */
    public function setDetails(?string $details): LogRecordInterface
    {
        return $this->setData(LogRecordInterface::DETAILS, $details);
    }

    /**
     * Get Details
     *
     * @return string|null
     */
    public function getDetails(): ?string
    {
        return $this->getData(LogRecordInterface::DETAILS);
    }

    /**
     * Set Store
     *
     * @param int|null $storeId
     * @return LogRecordInterface
     */
    public function setStoreId(?int $storeId): LogRecordInterface
    {
        return $this->setData(LogRecordInterface::STORE_ID, $storeId);
    }

    /**
     * Get Store
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(LogRecordInterface::STORE_ID);
    }

    /**
     * Get Result
     *
     * @return bool|null
     */
    public function getResult(): ?bool
    {
        return $this->getData(LogRecordInterface::RESULT) === null ?
            null :
            (bool)$this->getData(LogRecordInterface::RESULT);
    }

    /**
     * Set Result
     *
     * @param bool|null $result
     * @return LogRecordInterface
     */
    public function setResult(?bool $result): LogRecordInterface
    {
        return $this->setData(LogRecordInterface::RESULT, $result);
    }

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getSentAt(): ?string
    {
        return $this->getData(LogRecordInterface::SENT_AT);
    }

    /**
     * @return \MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface $extensionAttributes
     * @return LogRecordInterface
     */
    public function setExtensionAttributes(
        \MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface $extensionAttributes
    ): LogRecordInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
