<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface LogRecordInterface extends ExtensibleDataInterface
{
    /**
     * ID
     *
     * @var string
     */
    const LOGRECORD_ID = 'record_id';

    /**
     * Customer Email attribute constant
     *
     * @var string
     */
    const CUSTOMER_EMAIL = 'customer_email';

    /**
     * Email Template attribute constant
     *
     * @var string
     */
    const EMAIL_TEMPLATE_ID = 'email_template_id';

    /**
     * Count Of Products attribute constant
     *
     * @var string
     */
    const PRODUCT_COUNT = 'product_count';

    /**
     * Details attribute constant
     *
     * @var string
     */
    const DETAILS = 'details';

    /**
     * Store attribute constant
     *
     * @var string
     */
    const STORE_ID = 'store_id';

    /**
     * Creation time constant
     *
     * @var string
     */
    const SENT_AT = 'sent_at';

    /**
     * Result constant
     *
     * @var bool
     */
    const RESULT = 'result';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getLogRecordId(): ?int;

    /**
     * Set ID
     *
     * @param int|null $logRecordId
     * @return LogRecordInterface
     */
    public function setLogRecordId(?int $logRecordId): LogRecordInterface;

    /**
     * Get Customer Email
     *
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * Set Customer Email
     *
     * @param string|null $customerEmail
     * @return LogRecordInterface
     */
    public function setCustomerEmail(?string $customerEmail): LogRecordInterface;

    /**
     * Get Email Template
     *
     * @return string|null
     */
    public function getEmailTemplateId(): ?string;

    /**
     * Set Email Template
     *
     * @param string|null $emailTemplateId
     * @return LogRecordInterface
     */
    public function setEmailTemplateId(?string $emailTemplateId): LogRecordInterface;

    /**
     * Get Count of Products
     *
     * @return int|null
     */
    public function getProductCount(): ?int;

    /**
     * Set Count of Products
     *
     * @param int|null $productCount
     * @return LogRecordInterface
     */
    public function setProductCount(?int $productCount): LogRecordInterface;

    /**
     * Get Details
     *
     * @return string|null
     */
    public function getDetails(): ?string;

    /**
     * Set Details
     *
     * @param string|null $details
     * @return LogRecordInterface
     */
    public function setDetails(?string $details): LogRecordInterface;

    /**
     * Get Store
     *
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * Set Store
     *
     * @param int|null $storeId
     * @return LogRecordInterface
     */
    public function setStoreId(?int $storeId): LogRecordInterface;

    /**
     * Get Result
     *
     * @return bool|null
     */
    public function getResult(): ?bool;

    /**
     * Set Result
     *
     * @param bool|null $result
     * @return LogRecordInterface
     */
    public function setResult(?bool $result): LogRecordInterface;

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getSentAt(): ?string;

    /**
     * @return \MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface;

    /**
     * @param \MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface $extensionAttributes
     * @return LogRecordInterface
     */
    public function setExtensionAttributes(
        \MageWorx\ReviewReminderBase\Api\Data\LogRecordExtensionInterface $extensionAttributes
    ): LogRecordInterface;
}
