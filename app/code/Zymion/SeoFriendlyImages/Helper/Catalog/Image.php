<?php

namespace Zymion\SeoFriendlyImages\Helper\Catalog;

use Magento\Catalog\Model\Config\CatalogMediaConfig;

class Image extends \Magento\Catalog\Helper\Image
{
    /**
     * @var \Zymion\SeoFriendlyImages\Helper\Data
     */
    private $seoFriendlyImagesHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product\ImageFactory $productImageFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper
     * @param \Magento\Catalog\Model\View\Asset\PlaceholderFactory $placeholderFactory
     * @param CatalogMediaConfig $mediaConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\ImageFactory $productImageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Zymion\SeoFriendlyImages\Helper\Data $seoFriendlyImagesHelper,
        \Magento\Catalog\Model\View\Asset\PlaceholderFactory $placeholderFactory = null,
        CatalogMediaConfig $mediaConfig = null
    ) {
        parent::__construct(
            $context,
            $productImageFactory,
            $assetRepo,
            $viewConfig,
            $placeholderFactory,
            $mediaConfig
        );

        $this->seoFriendlyImagesHelper = $seoFriendlyImagesHelper;
    }

    /**
     * Initialize base image file
     *
     * @return $this
     */
    protected function initBaseFile()
    {
        $this->_getModel()->setProduct($this->getProduct());
        parent::initBaseFile();
        return $this;
    }
    
    /**
     * Return image label
     *
     * @return string
     */
    public function getLabel()
    {
        $label = $this->_product->getData($this->getType() . '_' . 'label');
        if ($this->seoFriendlyImagesHelper->overwriteAlt()
            && $this->seoFriendlyImagesHelper->isModuleEnabled()
        ) {
            if (empty($label)) {
                $label = $this->seoFriendlyImagesHelper->getSeoFriendlyAltText($this->_product);
            } else {
                $label = $this->seoFriendlyImagesHelper->replaceVariablesInFileName($label, $this->_product);
            }
        }
        if (empty($label)) {
            $label = $this->_product->getName();
        }
        return $label;
    }
}
