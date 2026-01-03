<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use MageWorx\XReviewBase\Api\Data\VoteExtensionInterface;
use MageWorx\XReviewBase\Api\Data\VoteInterface;

class Vote extends AbstractExtensibleModel implements VoteInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_xreviewbase_vote';

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
    protected $_eventPrefix = 'mageworx_xreviewbase_review_vote';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'review_vote';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Vote::class);
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
     * Get Vote ID
     *
     * @return array
     */
    public function getVoteId(): ?int
    {
        return $this->getData(VoteInterface::VOTE_ID) === null
            ? null
            : (int)$this->getData(VoteInterface::VOTE_ID);
    }

    /**
     * Set Vote ID
     *
     * @param int|null $voteId
     * @return VoteInterface
     */
    public function setVoteId(?int $voteId): VoteInterface
    {
        return $this->setData(VoteInterface::VOTE_ID, $voteId);
    }

    /**
     * Get Review ID
     *
     * @return array
     */
    public function getReviewId(): ?int
    {
        return $this->getData(VoteInterface::REVIEW_ID) === null ?
            null :
            (int)$this->getData(VoteInterface::REVIEW_ID);
    }

    /**
     * Set Review ID
     *
     * @param int|null $reviewId
     * @return VoteInterface
     */
    public function setReviewId(?int $reviewId): VoteInterface
    {
        return $this->setData(VoteInterface::REVIEW_ID, $reviewId);
    }

    /**
     * Set IP Address
     *
     * @param string $ipAddress
     * @return VoteInterface
     */
    public function setIpAddress(string $ipAddress): VoteInterface
    {
        return $this->setData(VoteInterface::IP_ADDRESS, $ipAddress);
    }

    /**
     * Get IP Address
     *
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->getData(VoteInterface::IP_ADDRESS);
    }

    /**
     * Set Like Count
     *
     * @param int $count
     * @return VoteInterface
     */
    public function setLikeCount(int $count): VoteInterface
    {
        return $this->setData(VoteInterface::LIKE_COUNT, $count);
    }

    /**
     * Get Like Count
     *
     * @return int|null
     */
    public function getLikeCount(): ?int
    {
        return $this->getData(VoteInterface::LIKE_COUNT);
    }

    /**
     * Set Dislike Count
     *
     * @param int $count
     * @return VoteInterface
     */
    public function setDislikeCount(int $count): VoteInterface
    {
        return $this->setData(VoteInterface::DISLIKE_COUNT, $count);
    }

    /**
     * Get Dislike Count
     *
     * @return int|null
     */
    public function getDislikeCount(): ?int
    {
        return $this->getData(VoteInterface::DISLIKE_COUNT);
    }

    /**
     * Get Creation Time
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(VoteInterface::CREATED_AT);
    }

    /**
     * @return \MageWorx\XReviewBase\Api\Data\VoteExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\XReviewBase\Api\Data\VoteExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \MageWorx\XReviewBase\Api\Data\VoteExtensionInterface $extensionAttributes
     * @return VoteInterface
     */
    public function setExtensionAttributes(\MageWorx\XReviewBase\Api\Data\VoteExtensionInterface $extensionAttributes): VoteInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
