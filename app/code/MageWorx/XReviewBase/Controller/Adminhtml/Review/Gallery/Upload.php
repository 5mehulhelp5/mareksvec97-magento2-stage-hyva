<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Controller\Adminhtml\Review\Gallery;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Upload
 */
class Upload extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = \Magento\Review\Controller\Adminhtml\Product::ADMIN_RESOURCE;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \MageWorx\XReviewBase\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \MageWorx\XReviewBase\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \MageWorx\XReviewBase\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->resultRawFactory  = $resultRawFactory;
        $this->serializer        = $serializer;
        $this->imageUploader     = $imageUploader;
    }

    /**
     * Upload image(s) to the review gallery.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $result = $this->imageUploader->uploadImageToTmp();

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents($this->serializer->serialize($result));

        return $response;
    }
}
