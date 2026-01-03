<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Block\Adminhtml\Helper\Form\Gallery;

use Magento\Backend\Block\DataProviders\ImageUploadConfig as ImageUploadConfigDataProvider;
use Magento\Backend\Block\Media\Uploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\MediaStorage\Helper\File\Storage\Database;

/**
 * Block for gallery content.
 */
class Content extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'MageWorx_XReviewBase::review/helper/gallery.phtml';

    /**
     * @var \MageWorx\XReviewBase\Model\Review\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var ImageUploadConfigDataProvider
     */
    protected $imageUploadConfigDataProvider;

    /**
     * @var Database
     */
    protected $fileStorageDatabase;

    /**
     * Content constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonEncoder
     * @param \MageWorx\XReviewBase\Model\Review\Media\Config $mediaConfig
     * @param ImageUploadConfigDataProvider $imageUploadConfigDataProvider
     * @param Database $fileStorageDatabase
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonEncoder,
        \MageWorx\XReviewBase\Model\Review\Media\Config $mediaConfig,
        ImageUploadConfigDataProvider $imageUploadConfigDataProvider,
        Database $fileStorageDatabase,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonEncoder                   = $jsonEncoder;
        $this->mediaConfig                   = $mediaConfig;
        $this->imageUploadConfigDataProvider = $imageUploadConfigDataProvider;
        $this->fileStorageDatabase           = $fileStorageDatabase;
        $this->imageHelper                   = $imageHelper;
    }

    /**
     * Prepare layout.
     *
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'uploader',
            \Magento\Backend\Block\Media\Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('mageworx_xreviewbase/review_gallery/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.jpg, .gif, .png)'),
                    'files' => ['*.jpg', '*.jpeg', '*.gif', '*.png'],
                ],
            ]
        );

        $this->_eventManager->dispatch('review_gallery_prepare_layout', ['block' => $this]);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve uploader block
     *
     * @return Uploader
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Retrieve uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * Returns js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Returns buttons for add image action.
     *
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * Returns image json
     *
     * @return string
     */
    public function getImagesJson()
    {
        $value = $this->getElement()->getImages();
        if (is_array($value) &&
            array_key_exists('images', $value) &&
            is_array($value['images']) &&
            !empty($value['images'])
        ) {
            $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $images   = $this->sortImagesByPosition($value['images']);
            foreach ($images as &$image) {
                $image['url'] = $this->mediaConfig->getMediaUrl($image['file']);
                if ($this->fileStorageDatabase->checkDbUsage() &&
                    !$mediaDir->isFile($this->mediaConfig->getMediaPath($image['file']))
                ) {
                    $this->fileStorageDatabase->saveFileToFilesystem(
                        $this->mediaConfig->getMediaPath($image['file'])
                    );
                }
                try {
                    $fileHandler   = $mediaDir->stat($this->mediaConfig->getMediaPath($image['file']));
                    $image['size'] = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    $image['url']  = $this->imageHelper->getDefaultPlaceholderUrl('small_image');
                    $image['size'] = 0;
                    $this->_logger->warning($e);
                }
            }

            return $this->jsonEncoder->serialize($images);
        }

        return '[]';
    }

    /**
     * Sort images array by position key
     *
     * @param array $images
     * @return array
     */
    protected function sortImagesByPosition($images)
    {
        if (is_array($images)) {
            usort(
                $images,
                function ($imageA, $imageB) {
                    return ($imageA['position'] < $imageB['position']) ? -1 : 1;
                }
            );
        }

        return $images;
    }
}
