<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface;

class Reminder extends AbstractExtensibleModel implements ReminderInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_reviewreminderbase_reminder';

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
    protected $_eventPrefix = 'mageworx_reviewreminderbase_reminder';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'reminder';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Reminder::class);
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
     * Get Reminder id
     *
     * @return int|null
     */
    public function getReminderId(): ?int
    {
        return $this->getData(ReminderInterface::REMINDER_ID) === null ?
            null :
            (int)$this->getData(ReminderInterface::REMINDER_ID);
    }

    /**
     * Set Reminder id
     *
     * @param int|null $reminderId
     * @return ReminderInterface
     */
    public function setReminderId(?int $reminderId)
    {
        return $this->setData(ReminderInterface::REMINDER_ID, $reminderId);
    }

    /**
     * Set Name
     *
     * @param string|null $name
     * @return ReminderInterface
     */
    public function setName(?string $name)
    {
        return $this->setData(ReminderInterface::NAME, $name);
    }

    /**
     * Get Name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(ReminderInterface::NAME);
    }

    /**
     * Set Type
     *
     * @param string|null $type
     * @return ReminderInterface
     */
    public function setType(?string $type)
    {
        return $this->setData(ReminderInterface::TYPE, $type);
    }

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getData(ReminderInterface::TYPE);
    }

    /**
     * Set Content
     *
     * @param string|null $content
     * @return ReminderInterface
     */
    public function setContent(?string $content)
    {
        return $this->setData(ReminderInterface::CONTENT, $content);
    }

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData(ReminderInterface::CONTENT);
    }

    /**
     * Set Email Template ID
     *
     * @param string|null $id
     * @return ReminderInterface
     */
    public function setEmailTemplateId(?string $id): ReminderInterface
    {
        return $this->setData(ReminderInterface::EMAIL_TEMPLATE_ID, $id);
    }

    /**
     * Get Email Template ID
     *
     * @return string|null
     */
    public function getEmailTemplateId(): ?string
    {
        return $this->getData(ReminderInterface::EMAIL_TEMPLATE_ID);
    }

    /**
     * Set Period
     *
     * @param int|null $period
     * @return ReminderInterface
     */
    public function setPeriod(?int $period)
    {
        return $this->setData(ReminderInterface::PERIOD, $period);
    }

    /**
     * Get Period
     *
     * @return int|null
     */
    public function getPeriod(): ?int
    {
        return $this->getData(ReminderInterface::PERIOD) === null ?
            null :
            (int)$this->getData(ReminderInterface::PERIOD);
    }

    /**
     * Set Display On Mobile
     *
     * @param bool|null $displayOnMobile
     * @return ReminderInterface
     */
    public function setDisplayOnMobile(?bool $displayOnMobile)
    {
        return $this->setData(ReminderInterface::DISPLAY_ON_MOBILE, $displayOnMobile);
    }

    /**
     * Get Display On Mobile
     *
     * @return bool|null
     */
    public function getDisplayOnMobile(): ?bool
    {
        return $this->getData(ReminderInterface::DISPLAY_ON_MOBILE) === null ?
            null :
            (bool)$this->getData(ReminderInterface::DISPLAY_ON_MOBILE);
    }

    /**
     * Set Priority
     *
     * @param int|null $priority
     * @return ReminderInterface
     */
    public function setPriority(?int $priority)
    {
        return $this->setData(ReminderInterface::PRIORITY, $priority);
    }

    /**
     * Get Priority
     *
     * @return int|null
     */
    public function getPriority(): ?int
    {
        return $this->getData(ReminderInterface::PRIORITY) === null ?
            null :
            (int)$this->getData(ReminderInterface::PRIORITY);
    }

    /**
     * Set Status
     *
     * @param bool|int $status
     * @return ReminderInterface
     */
    public function setStatus(?bool $status)
    {
        return $this->setData(ReminderInterface::STATUS, $status);
    }

    /**
     * Get Status
     *
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->getData(ReminderInterface::STATUS) === null ?
            null :
            (bool)$this->getData(ReminderInterface::STATUS);
    }

    /**
     * Get Store Ids
     *
     * @return array|null
     */
    public function getStoreIds(): ?array
    {
        return $this->getData(ReminderInterface::STORE_IDS);
    }

    /**
     * Set Store Ids
     *
     * @param array $storeIds
     * @return ReminderInterface
     */
    public function setStoreIds($storeIds): ReminderInterface
    {
        return $this->setData(ReminderInterface::STORE_IDS, $storeIds);
    }

    /**
     * Get Customer Group Ids
     *
     * @return array|null
     */
    public function getCustomerGroupIds(): ?array
    {
        return $this->getData(ReminderInterface::CUSTOMER_GROUP_IDS);
    }

    /**
     * Set Customer Group Ids
     *
     * @param array $customerGroupIds
     * @return ReminderInterface
     */
    public function setCustomerGroupIds(array $customerGroupIds): ReminderInterface
    {
        return $this->setData(ReminderInterface::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(ReminderInterface::CREATED_AT);
    }

    /**
     * Get Updating Time
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(ReminderInterface::UPDATED_AT);
    }

    /**
     * @return \MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface $extensionAttributes
     * @return ReminderInterface
     */
    public function setExtensionAttributes(
        \MageWorx\ReviewReminderBase\Api\Data\ReminderExtensionInterface $extensionAttributes
    ): ReminderInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
