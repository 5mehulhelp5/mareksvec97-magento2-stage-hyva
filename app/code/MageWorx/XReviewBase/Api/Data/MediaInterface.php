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
interface MediaInterface extends ExtensibleDataInterface
{
    /**
     * ID
     *
     * @var string
     */
    const VALUE_ID = 'value_id';

    /**
     * Review ID
     *
     * @var string
     */
    const REVIEW_ID = 'review_id';

    /**
     * Media Type
     *
     * @var string
     */
    const MEDIA_TYPE = 'media_type';

    /**
     * Media Type
     *
     * @var string
     */
    const FILE = 'file';

    /**
     * Media URL
     *
     * @var string
     */
    const MEDIA_URL = 'media_url';

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
    public function getMediaId(): ?int;

    /**
     * Set ID
     *
     * @param int|null $mediaId
     * @return mixed
     */
    public function setMediaId(?int $mediaId): MediaInterface;

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
    public function setReviewId(?int $reviewId): MediaInterface;

    /**
     * Get Media Type
     *
     * @return string|null
     */
    public function getMediaType(): ?string;

    /**
     * Set Media Type
     *
     * @param string|null $mediaType
     * @return MediaInterface
     */
    public function setMediaType(?string $mediaType): MediaInterface;

    /**
     * Get Media File Path
     *
     * @return string|null
     */
    public function getFile(): ?string;

    /**
     * Set Media File Path
     *
     * @param string|null $file
     * @return MediaInterface
     */
    public function setFile(?string $file): MediaInterface;

    /**
     * Get Media URL
     *
     * @return string|null
     */
    public function getMediaUrl(): ?string;

    /**
     * @return \MageWorx\XReviewBase\Api\Data\MediaExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\XReviewBase\Api\Data\MediaExtensionInterface;

    /**
     * @param \MageWorx\XReviewBase\Api\Data\MediaExtensionInterface $extensionAttributes
     * @return MediaInterface
     */
    public function setExtensionAttributes(
        \MageWorx\XReviewBase\Api\Data\MediaExtensionInterface $extensionAttributes
    ): MediaInterface;
}
