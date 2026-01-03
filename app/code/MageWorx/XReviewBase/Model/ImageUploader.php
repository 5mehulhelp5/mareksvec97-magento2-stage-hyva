<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class ImageUploader
{
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \MageWorx\XReviewBase\Model\Review\Media\Config
     */
    protected $reviewMediaConfig;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var array
     */
    protected $allowedMimeTypes = [
        'jpg'  => 'image/jpg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/png',
        'png'  => 'image/gif'
    ];

    /**
     * ImageUploader constructor.
     *
     * @param \Magento\Framework\Image\AdapterFactory $adapterFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Review\Media\Config $reviewMediaConfig
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \MageWorx\XReviewBase\Model\Review\Media\Config $reviewMediaConfig,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->adapterFactory    = $adapterFactory;
        $this->filesystem        = $filesystem;
        $this->reviewMediaConfig = $reviewMediaConfig;
        $this->uploaderFactory   = $uploaderFactory;
        $this->eventManager      = $eventManager;
    }

    /**
     * Upload image(s) to the review gallery.
     *
     * @return array
     */
    public function uploadImageToTmp($fileId = 'image')
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions($this->getAllowedExtensions());
            $imageAdapter = $this->adapterFactory->create();
            $uploader->addValidateCallback('product_review_image', $imageAdapter, 'validateUploadFile');
            $uploader->setFilesDispersion(true);
            $uploader->setAllowRenameFiles(true);

            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $result         = $uploader->save(
                $mediaDirectory->getAbsolutePath($this->reviewMediaConfig->getBaseTmpMediaPath())
            );

            $this->eventManager->dispatch(
                'mageworx_xreviewbase_review_upload_image_after',
                ['result' => $result, 'action' => $this]
            );

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url']  = $this->reviewMediaConfig->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $result;
    }

    /**
     * Get the set of allowed file extensions.
     *
     * @return array
     */
    private function getAllowedExtensions()
    {
        return array_keys($this->allowedMimeTypes);
    }
}
