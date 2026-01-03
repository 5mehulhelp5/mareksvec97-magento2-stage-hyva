<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use MageWorx\XReviewBase\Model\Review\Media\Config as MediaConfig;

class Image
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var MediaConfig
     */
    protected $mediaConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        MediaConfig $mediaConfig
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename)
    {
        if ($this->_mediaDirectory->isFile($filename)) {
            return true;
        }
        return false;
    }

    /**
     * @param \MageWorx\XReviewBase\Model\Media $image
     * @param null $width
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resize($image)
    {
        $absolutePath = $this->_mediaDirectory->getAbsolutePath(
            $this->mediaConfig->getMediaPath($image->getFile())
        );

        $imageResized = $this->_mediaDirectory->getAbsolutePath(
            $this->mediaConfig->getThumbnailMediaPath($image->getFile())
        );

        $width = $this->mediaConfig->getThumbnailWidth();

        if (!$this->_fileExists($this->mediaConfig->getThumbnailMediaPath($image->getFile()))) {
            $imageFactory = $this->_imageFactory->create();
            $imageFactory->open($absolutePath);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepTransparency(true);
            $imageFactory->keepFrame(false);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->resize($width);
            $imageFactory->save($imageResized);
        }

        return $this->mediaConfig->getThumbnailMediaUrl($image->getFile());
    }
}
