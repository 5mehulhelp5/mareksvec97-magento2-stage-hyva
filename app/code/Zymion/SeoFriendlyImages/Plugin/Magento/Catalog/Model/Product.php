<?php

namespace Zymion\SeoFriendlyImages\Plugin\Magento\Catalog\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class Product
{
    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var Product\Media\Config
     */
    protected $_catalogProductMediaConfig;

    /**
     * @var \Magento\MediaStorage\Service\ImageResize
     */
    private $_imageResize;

    /**
     * @var \Zymion\SeoFriendlyImages\Helper\Data
     */
    private $seoFriendlyImagesHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     * @param \Magento\MediaStorage\Service\ImageResize $imageResize
     * @param \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        \Magento\MediaStorage\Service\ImageResize $imageResize,
        \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper
    ) {
        $this->_filesystem = $filesystem;
        $this->_catalogProductMediaConfig = $catalogProductMediaConfig;
        $this->_imageResize = $imageResize;
        $this->seoFriendlyImagesHelper = $seoFriendlyImagesHelper;
    }

    /**
     * Overwrite the file names of the media gallery images.
     *
     * @param \Magento\Catalog\Model\Product $subject
     * @param array $images
     * @return $images
     */
    public function afterGetMediaGalleryImages(\Magento\Catalog\Model\Product $subject, $images)
    {
        if (!$this->seoFriendlyImagesHelper->isModuleEnabled()) {
            return $images;
        }

        $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $imageSubDirectory = $this->_catalogProductMediaConfig->getBaseMediaPath();

        foreach ($images as $image) {
            if (!isset($image['zymion_original_filename']) && $directory->isExist($imageSubDirectory.$image['file'])) {
                $newFile = $this->seoFriendlyImagesHelper->getSeoFriendlyImageName($subject, $image['file'], $image);

                $image['zymion_original_filename'] = $image['file'];
                $image['file'] = $newFile;
                $image['url'] = $this->_catalogProductMediaConfig->getMediaUrl($image['file']);
                $image['path'] = $directory->getAbsolutePath(
                    $this->_catalogProductMediaConfig->getMediaPath($image['file'])
                );

                $this->_imageResize->resizeFromImageName($newFile);
            }

            if ($this->seoFriendlyImagesHelper->overwriteAlt()
                && $this->seoFriendlyImagesHelper->isModuleEnabled()
            ) {
                if (empty($image['label'])) {
                    $image['label'] = $this->seoFriendlyImagesHelper->getSeoFriendlyAltText($subject);
                } else {
                    $image['label'] = $this->seoFriendlyImagesHelper->replaceVariablesInFileName(
                        $image['label'],
                        $subject
                    );
                }
            }
        }

        return $images;
    }
}
