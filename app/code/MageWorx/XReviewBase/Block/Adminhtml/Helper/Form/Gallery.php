<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Block\Adminhtml\Helper\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Gallery extends AbstractElement
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Gallery constructor.
     *
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Registry $registry,
        $data = []
    ) {
        $this->layout   = $layout;
        $this->registry = $registry;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        return $this->getContentHtml();
    }

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {
        /* @var $content \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content */
        $content = $this->layout->createBlock('MageWorx\XReviewBase\Block\Adminhtml\Helper\Form\Gallery\Content');
        $content->setId($this->getHtmlId() . '_content')->setElement($this);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMegiaGallery($galleryJs);

        return $content->toHtml();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return '';
    }

    /**
     * Retrieve data object related with form
     *
     * @return mixed
     */
    public function getDataObject()
    {
        return $this->getForm()->getDataObject();
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return '<tr><td class="value" colspan="3">' . $this->getElementHtml() . '</td></tr>';
    }

    /**
     * @return \Magento\Review\Model\Review
     */
    public function getReview()
    {
        return $this->registry->registry('review_data');
    }

    /**
     * Get review images
     *
     * @return array|null
     */
    public function getImages()
    {
        return $this->getDataObject()->getData('media_gallery') ?: null;
    }
}
