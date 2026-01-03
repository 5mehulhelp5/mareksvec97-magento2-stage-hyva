<?php

namespace Zymion\SeoFriendlyImages\Model\Product;

class Image extends \Magento\Catalog\Model\Product\Image
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var string
     */
    private $newFileFull;

    /**
     * @var \Zymion\SeoFriendlyImages\Helper\Data
     */
    private $seoFriendlyImagesHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\FileSystem $viewFileSystem
     * @param \Magento\Catalog\Model\View\Asset\ImageFactory $viewAssetImageFactory
     * @param \Magento\Catalog\Model\View\Asset\PlaceholderFactory $viewAssetPlaceholderFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Catalog\Model\Product\Image\ParamsBuilder $paramsBuilder
     * @throws \Magento\Framework\Exception\FileSystemException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Catalog\Model\View\Asset\ImageFactory $viewAssetImageFactory,
        \Magento\Catalog\Model\View\Asset\PlaceholderFactory $viewAssetPlaceholderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\SerializerInterface $serializer = null,
        \Magento\Catalog\Model\Product\Image\ParamsBuilder $paramsBuilder = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $catalogProductMediaConfig,
            $coreFileStorageDatabase,
            $filesystem,
            $imageFactory,
            $assetRepo,
            $viewFileSystem,
            $viewAssetImageFactory,
            $viewAssetPlaceholderFactory,
            $scopeConfig,
            $resource,
            $resourceCollection,
            $data,
            $serializer,
            $paramsBuilder
        );

        $this->seoFriendlyImagesHelper = $seoFriendlyImagesHelper;
    }

    /**
     * Set product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return $this
     * @throws \Exception
     */
    public function setBaseFile($file)
    {
        if (is_a($this->product, \Magento\Catalog\Model\Product::class)) {
            $newFile = $this->seoFriendlyImagesHelper->getSeoFriendlyImageName($this->product, $file);

            if ($newFile != $file) {
                $imageSubDirectory = $this->_catalogProductMediaConfig->getBaseMediaPath();
                $directory = DIRECTORY_SEPARATOR . $imageSubDirectory;
                $this->newFileFull = $directory . $newFile;
            }
        } else {
            $newFile = $file;
        }

        parent::setBaseFile($newFile);

        return $this;
    }

    /**
     * Save file
     *
     * @return $this
     */
    public function saveFile()
    {
        parent::saveFile();

        if ($this->seoFriendlyImagesHelper->isModuleEnabled() && $this->newFileFull) {
            $this->_mediaDirectory->delete($this->newFileFull);
        }

        return $this;
    }
}
