<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Model\Review\Media;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\XReviewBase\Model\ConfigProvider;

/**
 * Review media config.
 */
class Config
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * Config constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider
    ) {
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;
    }

    /**
     * @return int
     */
    public function getThumbnailWidth(): int
    {
        return (int)$this->configProvider->getImageSize();
    }

    /**
     * Get filesystem directory path for review images relative to the media directory.
     *
     * @return string
     */
    public function getBaseMediaPathAddition()
    {
        return 'mageworx/xreviewbase/images';
    }

    /**
     * Get web-based directory path for review images relative to the media directory.
     *
     * @return string
     */
    public function getBaseMediaUrlAddition()
    {
        return 'mageworx/xreviewbase/images';
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return 'mageworx/xreviewbase/images';
    }

    /**
     * @return string
     */
    public function getBaseThumbnailMediaPath($size = 300)
    {
        return $this->getBaseMediaPath() . '/cache/' . $size;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'mageworx/xreviewbase/images';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseThumbnailMediaUrl()
    {
        return $this->storeManager->getStore()
                                  ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseThumbnailMediaPath();
    }

    /**
     * Filesystem directory path of temporary review images relative to the media directory.
     *
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return 'tmp/' . $this->getBaseMediaPathAddition();
    }

    /**
     * Get temporary base media URL.
     *
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_MEDIA
        ) . 'tmp/' . $this->getBaseMediaUrlAddition();
    }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . '/' . $this->prepareFile($file);
    }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getThumbnailMediaUrl($file)
    {
        return $this->getBaseThumbnailMediaUrl() . '/' . $this->prepareFile($file);
    }

    /**
     * @param $file
     * @return string
     */
    public function getMediaPath($file)
    {
        return $this->getBaseMediaPath() . '/' . $this->prepareFile($file);
    }

    /**
     * @return string
     */
    public function getThumbnailMediaPath($file)
    {
        return $this->getBaseThumbnailMediaPath() . '/' . $this->prepareFile($file);
    }

    /**
     * Get temporary media URL.
     *
     * @param string $file
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->prepareFile($file);
    }

    /**
     * Part of URL of temporary review images relative to the media directory.
     *
     * @param string $file
     * @return string
     */
    public function getTmpMediaShortUrl($file)
    {
        return 'tmp/' . $this->getBaseMediaUrlAddition() . '/' . $this->prepareFile($file);
    }

    /**
     * Part of URL of review images relatively to media folder.
     *
     * @param string $file
     * @return string
     */
    public function getMediaShortUrl($file)
    {
        return $this->getBaseMediaUrlAddition() . '/' . $this->prepareFile($file);
    }

    /**
     * Get path to the temporary media.
     *
     * @param string $file
     * @return string
     */
    public function getTmpMediaPath($file)
    {
        return $this->getBaseTmpMediaPath() . '/' . $this->prepareFile($file);
    }

    /**
     * Process file path.
     *
     * @param string $file
     * @return string
     */
    protected function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
}
