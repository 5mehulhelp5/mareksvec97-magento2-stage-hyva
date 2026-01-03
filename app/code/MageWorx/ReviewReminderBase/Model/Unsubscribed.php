<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\Model\AbstractModel;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterface;

class Unsubscribed extends AbstractModel implements UnsubscribedInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_reviewreminderbase_unsubscribed';

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
    protected $_eventPrefix = 'mageworx_reviewreminderbase_unsubscribed';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'unsubscribed';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Unsubscribed::class);
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
     * Get Unsubscribed id
     *
     * @return int|null
     */
    public function getUnsubscribedId(): ?int
    {
        return $this->getData(UnsubscribedInterface::UNSUBSCRIBED_ID) === null ?
            null :
            (int)$this->getData(UnsubscribedInterface::UNSUBSCRIBED_ID);
    }

    /**
     * Set Unsubscribed id
     *
     * @param int|null $unsubscribedId
     * @return UnsubscribedInterface
     */
    public function setUnsubscribedId(?int $unsubscribedId)
    {
        return $this->setData(
            UnsubscribedInterface::UNSUBSCRIBED_ID,
            $unsubscribedId
        );
    }

    /**
     * Set Email
     *
     * @param string|null $email
     * @return UnsubscribedInterface
     */
    public function setEmail(?string $email)
    {
        return $this->setData(UnsubscribedInterface::EMAIL, $email);
    }

    /**
     * Get Email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData(UnsubscribedInterface::EMAIL);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getUnsubscribedAt(): ?string
    {
        return $this->getData(UnsubscribedInterface::UNSUBSCRIBED_AT);
    }

    /**
     * @return \MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface $extensionAttributes
     * @return UnsubscribedInterface
     */
    public function setExtensionAttributes(
        \MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface $extensionAttributes
    ): UnsubscribedInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
