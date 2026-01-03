<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Observer;

class RemoveMediaOnReviewRemovingObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\Review\Media\Processor
     */
    protected $mediaProcessor;

    /**
     * RemoveMediaOnReviewRemovingObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\Review\Media\Processor $mediaProcessor
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\Review\Media\Processor $mediaProcessor
    ) {
        $this->mediaProcessor = $mediaProcessor;
    }

    /**
     * Function executes after review is saved
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getEvent()->getObject();

        if ($review->getEntityId() == $review->getEntityIdByCode('product')) {
            $this->mediaProcessor->removeAllMedia($review);
        }
    }
}
