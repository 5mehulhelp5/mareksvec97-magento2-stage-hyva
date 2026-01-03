<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use MageWorx\XReviewBase\Api\Data\MediaInterface;
use MageWorx\XReviewBase\Model\Review\Media\Config as MediaConfig;

class Media extends AbstractExtensibleModel implements MediaInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageworx_xreviewbase_media';

    /**
     * @var MediaConfig
     */
    protected $mediaConfig;

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
    protected $_eventPrefix = 'mageworx_xreviewbase_media';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'media';

    /**
     * @var \MageWorx\XReviewBase\Helper\Image
     */
    protected $imageHelper;

    /**
     * Media constructor.
     *
     * @param MediaConfig $mediaConfig
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \MageWorx\XReviewBase\Helper\Image $imageHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        MediaConfig $mediaConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \MageWorx\XReviewBase\Helper\Image $imageHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->mediaConfig = $mediaConfig;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Media::class);
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
     * Get Media ID
     *
     * @return array
     */
    public function getMediaId(): ?int
    {
        return $this->getData(MediaInterface::VALUE_ID) === null ?
            null :
            (int)$this->getData(MediaInterface::VALUE_ID);
    }

    /**
     * Set Media ID
     *
     * @param int|null $mediaId
     * @return MediaInterface
     */
    public function setMediaId(?int $mediaId): MediaInterface
    {
        return $this->setData(MediaInterface::VALUE_ID, $mediaId);
    }

    /**
     * Get Review ID
     *
     * @return array
     */
    public function getReviewId(): ?int
    {
        return $this->getData(MediaInterface::REVIEW_ID) === null ?
            null :
            (int)$this->getData(MediaInterface::REVIEW_ID);
    }

    /**
     * Set Review ID
     *
     * @param int|null $reviewId
     * @return MediaInterface
     */
    public function setReviewId(?int $reviewId): MediaInterface
    {
        return $this->setData(MediaInterface::VALUE_ID, $reviewId);
    }

    /**
     * Set Media Type
     *
     * @param string|null $mediaType
     * @return MediaInterface
     */
    public function setMediaType(?string $mediaType): MediaInterface
    {
        return $this->setData(MediaInterface::MEDIA_TYPE, $mediaType);
    }

    /**
     * Get Media Type
     *
     * @return string|null
     */
    public function getMediaType(): ?string
    {
        return $this->getData(MediaInterface::MEDIA_TYPE);
    }

    /**
     * Set Media File Path
     *
     * @param string|null $file
     * @return MediaInterface
     */
    public function setFile(?string $file): MediaInterface
    {
        return $this->setData(MediaInterface::FILE, $file);
    }

    /**
     * Get Media File Path
     *
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->getData(MediaInterface::FILE);
    }

    /**
     * Get Media URL
     *
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl(): ?string
    {
        if ($this->getFile()) {
            return $this->mediaConfig->getMediaUrl($this->getFile());
        }

        return null;
    }

    /**
     * Get Thumbnail Media URL
     *
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getThumbnailMediaUrl(): ?string
    {
        if ($this->getFile()) {
            return $this->imageHelper->resize($this);
        }

        return null;
    }

    /**
     * @return \MageWorx\XReviewBase\Api\Data\MediaExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\MageWorx\XReviewBase\Api\Data\MediaExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \MageWorx\XReviewBase\Api\Data\MediaExtensionInterface $extensionAttributes
     * @return MediaInterface
     */
    public function setExtensionAttributes(
        \MageWorx\XReviewBase\Api\Data\MediaExtensionInterface $extensionAttributes
    ): MediaInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
