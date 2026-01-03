<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Observer\Adminhtml;

class SaveReviewMediaObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\Review\Media\Processor
     */
    protected $mediaProcessor;

    /**
     * SaveReviewMediaObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\Review\Media\Processor $mediaProcessor
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\Review\Media\Processor $mediaProcessor
    ) {
        $this->mediaProcessor = $mediaProcessor;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getEvent()->getObject();

        if ($review->getEntityId() == $review->getEntityIdByCode('product')) {
            $this->mediaProcessor->saveMedia($review);
        }
    }
}
