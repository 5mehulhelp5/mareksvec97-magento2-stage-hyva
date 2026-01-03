<?php

namespace Zymion\SeoFriendlyImages\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_SEOFRIENDLYIMAGES_ENABLED = 'seofriendlyimages/general/enabled';
    public const XML_PATH_SEOFRIENDLYIMAGES_USE_SYMLINK = 'seofriendlyimages/general/use_symlink';
    public const XML_PATH_SEOFRIENDLYIMAGES_APPEND_CATEGORY = 'seofriendlyimages/general/append_category';
    public const XML_PATH_SEOFRIENDLYIMAGES_CUSTOM_PATTERN = 'seofriendlyimages/general/custom_pattern';
    public const XML_PATH_SEOFRIENDLYIMAGES_FILENAME_PATTERN = 'seofriendlyimages/general/filename_pattern';
    public const XML_PATH_SEOFRIENDLYIMAGES_OVERWRITE_ALT = 'seofriendlyimages/general/overwrite_alt';
    public const XML_PATH_SEOFRIENDLYIMAGES_ALT_PATTERN = 'seofriendlyimages/general/alt_pattern';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var GalleryReadHandler
     */
    private $_galleryReadHandler;

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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productModelFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * Helper constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param GalleryReadHandler $galleryReadHandler
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Product\Media\Config $catalogProductMediaConfig
     * @param ProductFactory $productModelFactory
     * @param \Magento\Framework\Filesystem\Io\File $file
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        GalleryReadHandler $galleryReadHandler,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        ProductFactory $productModelFactory,
        \Magento\Framework\Filesystem\Io\File $file
    ) {
        parent::__construct($context);

        $this->_registry = $registry;
        $this->_galleryReadHandler = $galleryReadHandler;
        $this->_filesystem = $filesystem;
        $this->_catalogProductMediaConfig = $catalogProductMediaConfig;
        $this->productModelFactory = $productModelFactory;
        $this->file = $file;
    }

    /**
     * Get the SEO friendly name of an image
     *
     * @param Product $product
     * @param string $originalFilePath
     * @param null|array $image
     * @return string
     */
    public function getSeoFriendlyImageName(Product $product, $originalFilePath, $image = null)
    {
        $newFile = $originalFilePath;

        if (!$this->isModuleEnabled()) {
            return $newFile;
        }

        $mediaGalleryImage = $image ?: $this->getMediaGalleryImage($product, $originalFilePath);
        $imageId = $mediaGalleryImage ? $mediaGalleryImage['id'] : null;
        $imageSubDirectory = $this->_catalogProductMediaConfig->getBaseMediaPath();
        $extension = $this->file->getPathInfo($originalFilePath)['extension'];
        $directory = DIRECTORY_SEPARATOR . $imageSubDirectory;

        if ($originalFilePath
            && $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->isExist($directory . $originalFilePath)
        ) {
            $newFile = $this->file->getPathInfo($originalFilePath)['dirname'] . '-' . $product->getId();
            if ($imageId) {
                $newFile .= '-' . $imageId;
            }
            $newFile .= DIRECTORY_SEPARATOR;

            $newFileName = $this->getNewFileName($product, $mediaGalleryImage);

            $cleanFileName = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities($newFileName));
            $newFile .= strtolower(preg_replace('/\W+/', '-', $cleanFileName));
            $newFile .= '.' . $extension;

            if (!$this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->isExist($directory . $newFile)) {
                if ($this->useSymlinks()) {
                    $this->_filesystem
                        ->getDirectoryWrite(DirectoryList::MEDIA)
                        ->createSymlink($directory . $originalFilePath, $directory . $newFile);
                } else {
                    $this->_filesystem
                        ->getDirectoryWrite(DirectoryList::MEDIA)
                        ->copyFile($directory . $originalFilePath, $directory . $newFile);
                }
            }
        }

        return $newFile;
    }

    /**
     * Get the seo friendly alt text of an image
     *
     * @param Product $product
     * @return string
     */
    public function getSeoFriendlyAltText(Product $product)
    {
        return $this->replaceVariablesInFileName($this->altPattern(), $product);
    }

    /**
     * Get the new file name to be applied for images
     *
     * @param Product $product
     * @param array $mediaGalleryImage
     * @return string
     */
    private function getNewFileName(Product $product, $mediaGalleryImage)
    {
        if ($this->customPattern()) {
            $newFileName = $this->getPatternFileName($product, $mediaGalleryImage);
        } else {
            $newFileName = $this->getBasicFileName($product, $mediaGalleryImage);
        }

        $cleanFileName = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities($newFileName));
        return strtolower(preg_replace('/\W+/', '-', $cleanFileName));
    }

    /**
     * Get the new file name to be applied for images when using a custom pattern
     *
     * @param Product $product
     * @param array $mediaGalleryImage
     * @return string
     */
    private function getPatternFileName(Product $product, $mediaGalleryImage)
    {
        if ($mediaGalleryImage
            && ($mediaGalleryImage['filename_overwrite'] || $mediaGalleryImage['filename_overwrite_default'])
        ) {
            $newFileName = $mediaGalleryImage['filename_overwrite']
                ?? $mediaGalleryImage['filename_overwrite_default'];
        } else {
            $newFileName = $this->filenamePattern();
        }

        return $this->replaceVariablesInFileName($newFileName, $product);
    }

    /**
     * Replace variables in a filename by actual values
     *
     * @param string $filename
     * @param Product $product
     * @return string
     */
    public function replaceVariablesInFileName(string $filename, Product $product)
    {
        return preg_replace_callback(
            '/{{([^{}]*)}}/',
            function ($match) use ($product) {
                $variable = $match[1];

                if (str_starts_with($variable, 'product.')) {
                    try {
                        $value = $product->getAttributeText(str_replace('product.', '', $variable));

                        if (!$value) {
                            $value = $product->getData(str_replace('product.', '', $variable));
                        }
                    } catch (\Throwable $e) {
                        $value = $product->getData(str_replace('product.', '', $variable));
                    }
                    return is_array($value) ? implode('-', $value) : $value;
                } elseif (str_starts_with($variable, 'category.') && $category = $this->getCategory($product)) {
                    return $category->getData(str_replace('category.', '', $variable));
                }

                return '';
            },
            $filename
        );
    }

    /**
     * Get the new file name to be applied for images when not using a custom pattern
     *
     * @param Product $product
     * @param array $mediaGalleryImage
     * @return string
     */
    private function getBasicFileName(Product $product, $mediaGalleryImage)
    {
        if ($mediaGalleryImage
            && ($mediaGalleryImage['filename_overwrite'] || $mediaGalleryImage['filename_overwrite_default'])
        ) {
            $newFileName = $mediaGalleryImage['filename_overwrite']
                ?? $mediaGalleryImage['filename_overwrite_default'];
        } else {
            $newFileName = $product->getName();
        }

        if ($this->appendCategory() && $category = $this->getCategory($product)) {
            $newFileName .= '-' . $category->getName();
        }

        return $newFileName;
    }

    /**
     * Get the category of the product (in case of multiples it will return the first one)
     *
     * @param Product $product
     * @return string
     */
    private function getCategory(Product $product)
    {
        if (!$this->_registry) {
            return null;
        }

        $category = $this->_registry->registry('current_category');

        if (!$category && $product) {
            $categoryCollection = $product->getCategoryCollection();

            if ($categoryCollection) {
                $category = $categoryCollection->addAttributeToSelect('name')->getFirstItem();
            }
        }

        if ($category) {
            return $category;
        }

        return null;
    }

    /**
     * Get the media gallery image of an image by its file
     *
     * @param Product $product
     * @param string $file
     * @return string
     */
    private function getMediaGalleryImage(Product $product, $file)
    {
        $this->addMediaGallery($product);
        $mediaGalleryImages = $product->getMediaGalleryImages();

        if (!$mediaGalleryImages) {
            $prod = $this->productModelFactory->create()->load($product->getId());
            $this->addMediaGallery($prod);
            $mediaGalleryImages = $prod->getMediaGalleryImages();
        }

        if ($mediaGalleryImages) {
            foreach ($mediaGalleryImages as $image) {
                if ($image['file'] == $file || $image['zymion_original_filename'] == $file) {
                    return $image;
                }
            }
        }

        return null;
    }

    /**
     * Add media gallery to a product
     *
     * @param Product $product
     */
    private function addMediaGallery($product)
    {
        $this->_galleryReadHandler->execute($product);
    }

    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param integer|null $storeId
     * @return mixed
     */
    private function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->getConfig(self::XML_PATH_SEOFRIENDLYIMAGES_ENABLED);
    }

    /**
     * Check if symlinks should be used
     *
     * @return bool
     */
    public function useSymlinks()
    {
        return $this->getConfig(self::XML_PATH_SEOFRIENDLYIMAGES_USE_SYMLINK);
    }

    /**
     * Checks if the category should be appended to the filename
     *
     * @return bool
     */
    public function appendCategory()
    {
        return $this->getConfig(self::XML_PATH_SEOFRIENDLYIMAGES_APPEND_CATEGORY);
    }

    /**
     * Checks if the custom pattern should be used
     *
     * @return bool
     */
    public function customPattern()
    {
        return $this->getConfig(self::XML_PATH_SEOFRIENDLYIMAGES_CUSTOM_PATTERN);
    }

    /**
     * Retrieve the filename pattern to be used for the custom pattern
     *
     * @return string
     */
    public function filenamePattern()
    {
        return $this->getConfig(self::XML_PATH_SEOFRIENDLYIMAGES_FILENAME_PATTERN);
    }

    /**
     * Checks if the alt text should be overwritten
     *
     * @return bool
     */
    public function overwriteAlt()
    {
        return $this->getConfig(self::XML_PATH_SEOFRIENDLYIMAGES_OVERWRITE_ALT);
    }

    /**
     * Retrieve the alt text pattern to be used for overwriting
     *
     * @return string
     */
    public function altPattern()
    {
        return $this->getConfig(self::XML_PATH_SEOFRIENDLYIMAGES_ALT_PATTERN);
    }
}
