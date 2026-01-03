<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\XReviewBase\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface VoteInterface extends ExtensibleDataInterface
{
    /**
     * ID
     *
     * @var string
     */
    const VOTE_ID = 'vote_id';

    /**
     * Review ID
     *
     * @var string
     */
    const REVIEW_ID = 'review_id';

    /**
     * IP Address
     *
     * @var string
     */
    const IP_ADDRESS = 'ip_address';

    /**
     * Like Count
     *
     * @var string
     */
    const LIKE_COUNT = 'like_count';

    /**
     * Dislike Count
     *
     * @var string
     */
    const DISLIKE_COUNT = 'dislike_count';

    /**
     * Creation time constant
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Review ID
     *
     * @return int|null
     */
    public function getReviewId(): ?int;

    /**
     * Set ID
     *
     * @param int|null $reviewId
     * @return mixed
     */
    public function setReviewId(?int $reviewId): VoteInterface;

    /**
     * Get IP Address
     *
     * @return string|null
     */
    public function getIpAddress(): ?string;

    /**
     * Set IP Address
     *
     * @param string $ipAddress
     * @return VoteInterface
     */
    public function setIpAddress(string $ipAddress): VoteInterface;

    /**
     * Get Like Count
     * @return int|null
     */
    public function getLikeCount(): ?int;

    /**
     * Set Like Count
     *
     * @param int $count
     * @return VoteInterface
     */
    public function setLikeCount(int $count): VoteInterface;

    /**
     * Get Dislike Count
     * @return int|null
     */
    public function getDislikeCount(): ?int;

    /**
     * Set Dislike Count
     *
     * @param int $count
     * @return VoteInterface
     */
    public function setDislikeCount(int $count): VoteInterface;

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @return \MageWorx\XReviewBase\Api\Data\VoteExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\XReviewBase\Api\Data\VoteExtensionInterface;

    /**
     * @param \MageWorx\XReviewBase\Api\Data\VoteExtensionInterface $extensionAttributes
     * @return VoteInterface
     */
    public function setExtensionAttributes(
        \MageWorx\XReviewBase\Api\Data\VoteExtensionInterface $extensionAttributes
    ): VoteInterface;
}
