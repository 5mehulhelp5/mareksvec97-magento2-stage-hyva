<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model\Review\Media;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;

class Processor
{
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var \MageWorx\XReviewBase\Model\Review\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $fileStorageDb;

    /**
     * @var \MageWorx\XReviewBase\Model\ResourceModel\Media
     */
    protected $mediaResource;

    /**
     * @var \MageWorx\XReviewBase\Model\MediaFactory
     */
    protected $mediaFactory;

    /**
     * Processor constructor.
     *
     * @param \MageWorx\XReviewBase\Model\ResourceModel\Media $mediaResource
     * @param \MageWorx\XReviewBase\Model\MediaFactory $mediaFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param Config $mediaConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\ResourceModel\Media $mediaResource,
        \MageWorx\XReviewBase\Model\MediaFactory $mediaFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \MageWorx\XReviewBase\Model\Review\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->mediaConfig    = $mediaConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileStorageDb  = $fileStorageDb;
        $this->mediaResource  = $mediaResource;
        $this->mediaFactory   = $mediaFactory;
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return \Magento\Review\Model\Review
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function saveMedia($review)
    {
        $value = $review->getData('media_gallery');

        if (!is_array($value) || !isset($value['images'])) {
            return $review;
        }

        if (!is_array($value['images']) && strlen($value['images']) > 0) {
            $value['images'] = $this->jsonSerializer->unserialize($value['images']);
        }

        if (!is_array($value['images'])) {
            $value['images'] = [];
        }

        foreach ($value['images'] as &$image) {

            $image['review_id']  = $review->getId();
            $image['product_id'] = $review->getEntityPkValue();

            if (empty($image['value_id'])) {
                $newFile           = $this->moveImageFromTmp($image['file']);
                $image['new_file'] = $newFile;
                $image['file']     = $newFile;
            }
        }

        $review->setData('media_gallery', $value);

        if (!is_array($value) || !isset($value['images'])) {
            return $review;
        }

        if (!$review->isObjectNew()) {
            $this->processDeletedImages($value['images']);
        }

        $this->processNewAndExistingImages($value['images']);

        $review->setData('media_gallery', $value);

        return $review;
    }

    /**
     * Mark all images as deleted and remove them
     *
     * @param \Magento\Review\Model\Review $review
     * @return mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function removeAllMedia($review)
    {
        $value = $review->getData('media_gallery');

        if (!is_array($value) || !isset($value['images'])) {
            return $review;
        }

        if (!is_array($value['images'])) {
            $value['images'] = [];
        }

        foreach ($value['images'] as &$image) {

            $image['review_id']  = $review->getId();
            $image['product_id'] = $review->getEntityPkValue();
            $image['removed']    = 1;
        }

        $this->processDeletedImages($value['images']);
    }

    /**
     * @param array $images
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function processDeletedImages(array &$images)
    {
        $filesToDelete   = [];
        $recordsToDelete = [];

        foreach ($images as &$image) {
            if (!empty($image['removed'])) {
                if (!empty($image['value_id'])) {
                    if (preg_match('/\.\.(\\\|\/)/', $image['file'])) {
                        continue;
                    }
                    $recordsToDelete[] = $image['value_id'];
                    $path              = $this->mediaConfig->getBaseMediaPath();
                    $isFile            = $this->mediaDirectory->isFile($path . $image['file']);
                    if ($isFile) {
                        $filesToDelete[] = ltrim($image['file'], '/');
                    }
                }
            }
        }

        $this->mediaResource->deleteMedia($recordsToDelete);

        $this->removeDeletedImages($filesToDelete);
    }

    /**
     * @param array $files
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function removeDeletedImages(array $files)
    {
        $path = $this->mediaConfig->getBaseMediaPath();

        foreach ($files as $filePath) {
            $this->mediaDirectory->delete($path . '/' . $filePath);
        }
    }

    /**
     * Process images
     *
     * @param array $images
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function processNewAndExistingImages(array &$images)
    {
        foreach ($images as &$image) {

            if (empty($image['removed'])) {

                if (!empty($image['value_id'])) {
                    $this->mediaResource->deleteMedia($image['value_id']);
                }

                $this->processNewImage($image);
            }
        }
    }

    /**
     * Processes image as new.
     *
     * @param array $imageData
     * @return \MageWorx\XReviewBase\Model\Media
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function processNewImage(array $imageData)
    {
        $imageData['value_id'] = null;

        $media = $this->mediaFactory->create();
        $media->setData($imageData);
        $this->mediaResource->save($media);

        return $media;
    }

    /**
     * Move image from temporary directory to regular
     *
     * @param string $file
     * @return string|string[]
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function moveImageFromTmp($file)
    {
        $file            = $this->getFilenameFromTmp($this->getSafeFilename($file));
        $destinationFile = $this->getUniqueFileName($file);

        if ($this->fileStorageDb->checkDbUsage()) {
            $this->fileStorageDb->renameFile(
                $this->mediaConfig->getTmpMediaShortUrl($file),
                $this->mediaConfig->getMediaShortUrl($destinationFile)
            );

            $this->mediaDirectory->delete($this->mediaConfig->getTmpMediaPath($file));
            $this->mediaDirectory->delete($this->mediaConfig->getMediaPath($destinationFile));
        } else {
            $this->mediaDirectory->renameFile(
                $this->mediaConfig->getTmpMediaPath($file),
                $this->mediaConfig->getMediaPath($destinationFile)
            );
        }

        return str_replace('\\', '/', $destinationFile);
    }

    /**
     * Returns safe filename for posted image
     *
     * @param string $file
     * @return string
     */
    protected function getSafeFilename($file)
    {
        $file = DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);

        return $this->mediaDirectory->getDriver()->getRealPathSafety($file);
    }

    /**
     * Returns file name according to tmp name
     *
     * @param string $file
     * @return string
     */
    protected function getFilenameFromTmp($file)
    {
        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            return substr($file, 0, strlen($file) - 4);
        }

        return $file;
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param string $file
     * @param bool $forTmp
     * @return string
     */
    protected function getUniqueFileName($file, $forTmp = false)
    {
        if ($this->fileStorageDb->checkDbUsage()) {

            $destFilePath = $this->fileStorageDb->getUniqueFilename(
                $this->mediaConfig->getBaseMediaUrlAddition(),
                $file
            );
        } else {
            $destinationFile = $forTmp
                ? $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getTmpMediaPath($file))
                : $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getMediaPath($file));

            $destFilePath = dirname($file) . '/' . FileUploader::getNewFileName($destinationFile);
        }

        return $destFilePath;
    }
}
