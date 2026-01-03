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
interface ReminderInterface extends ExtensibleDataInterface
{
    /**
     * ID
     *
     * @var string
     */
    const REMINDER_ID = 'reminder_id';

    /**
     * Name attribute constant
     *
     * @var string
     */
    const NAME = 'name';

    /**
     * Type attribute constant
     *
     * @var string
     */
    const TYPE = 'type';

    /**
     * Content attribute constant
     *
     * @var string
     */
    const CONTENT = 'content';

    /**
     * Email Template ID attribute constant
     */
    const EMAIL_TEMPLATE_ID = 'email_template_id';

    /**
     * Period attribute constant
     *
     * @var string
     */
    const PERIOD = 'period';

    /**
     * Display On Mobile attribute constant
     *
     * @var string
     */
    const DISPLAY_ON_MOBILE = 'display_on_mobile';

    /**
     * Priority attribute constant
     *
     * @var string
     */
    const PRIORITY = 'priority';

    /**
     * Status attribute constant
     *
     * @var string
     */
    const STATUS = 'status';

    /**
     * Store IDs attribute constant
     *
     * @var string
     */
    const STORE_IDS = 'store_ids';

    /**
     * Customer group IDs attribute constant
     *
     * @var string
     */
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';

    /**
     * Creation time constant
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * Updated time constant
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getReminderId(): ?int;

    /**
     * Set ID
     *
     * @param int|null $reminderId
     * @return ReminderInterface
     */
    public function setReminderId(?int $reminderId);

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set Name
     *
     * @param string|null $name
     * @return ReminderInterface
     */
    public function setName(?string $name);

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Set Type
     *
     * @param string|null $type
     * @return ReminderInterface
     */
    public function setType(?string $type);

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Set Content
     *
     * @param string|null $content
     * @return ReminderInterface
     */
    public function setContent(?string $content);

    /**
     * Get Email Template ID
     *
     * @return string|null
     */
    public function getEmailTemplateId(): ?string;

    /**
     * Set Email Template ID
     *
     * @param string|null $id
     * @return ReminderInterface
     */
    public function setEmailTemplateId(?string $id): ReminderInterface;

    /**
     * Get Period
     *
     * @return int|null
     */
    public function getPeriod(): ?int;

    /**
     * Set Period
     *
     * @param int|null $period
     * @return ReminderInterface
     */
    public function setPeriod(?int $period);

    /**
     * Get Display On Mobile
     *
     * @return bool|null
     */
    public function getDisplayOnMobile(): ?bool;

    /**
     * Set Display On Mobile
     *
     * @param bool|null $displayOnMobile
     * @return ReminderInterface
     */
    public function setDisplayOnMobile(?bool $displayOnMobile);

    /**
     * Get Priority
     *
     * @return int|null
     */
    public function getPriority(): ?int;

    /**
     * Set Priority
     *
     * @param int|null $priority
     * @return ReminderInterface
     */
    public function setPriority(?int $priority);

    /**
     * Get Status
     *
     * @return bool|null
     */
    public function getStatus(): ?bool;

    /**
     * Set Status
     *
     * @param bool|null $status
     * @return ReminderInterface
     */
    public function setStatus(?bool $status);

    /**
     * Get Store Ids
     *
     * @return array|null
     */
    public function getStoreIds(): ?array;

    /**
     * Set Store Ids
     *
     * @param array $storeIds
     * @return ReminderInterface
     */
    public function setStoreIds($storeIds): ReminderInterface;

    /**
     * Get Customer Group Ids
     *
     * @return array|null
     */
    public function getCustomerGroupIds(): ?array;

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Get updating time
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set Customer Group Ids
     *
     * @param array $customerGroupIds
     * @return ReminderInterface
     */
    public function setCustomerGroupIds(array $customerGroupIds): ReminderInterface;

    /**
     * @return \MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface;

    /**
     * @param \MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface $extensionAttributes
     * @return ReminderInterface
     */
    public function setExtensionAttributes(
        \MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface $extensionAttributes
    ): ReminderInterface;
}
