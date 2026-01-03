<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface UnsubscribedInterface extends ExtensibleDataInterface
{
    /**
     * ID
     *
     * @var string
     */
    const UNSUBSCRIBED_ID = 'unsubscribed_id';

    /**
     * Email attribute constant
     *
     * @var string
     */
    const EMAIL = 'email';

    /**
     * Creation time attribute constant
     *
     * @var string
     */
    const UNSUBSCRIBED_AT = 'unsubscribed_at';

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
    public function getUnsubscribedId(): ?int;

    /**
     * Set ID
     *
     * @param int|null $unsubscribedId
     * @return UnsubscribedInterface
     */
    public function setUnsubscribedId(?int $unsubscribedId);

    /**
     * Get Email
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * Set Email
     *
     * @param string|null $email
     * @return UnsubscribedInterface
     */
    public function setEmail(?string $email);

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getUnsubscribedAt(): ?string;

    /**
     * @return \MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface;

    /**
     * @param \MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface $extensionAttributes
     * @return UnsubscribedInterface
     */
    public function setExtensionAttributes(
        \MageWorx\ReviewReminderBase\Api\Data\UnsubscribedExtensionInterface $extensionAttributes
    ): UnsubscribedInterface;
}
