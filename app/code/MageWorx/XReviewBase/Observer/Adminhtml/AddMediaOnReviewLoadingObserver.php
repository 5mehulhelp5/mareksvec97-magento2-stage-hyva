<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Observer\Adminhtml;

class AddMediaOnReviewLoadingObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\AddMediaToReview
     */
    protected $addMediaToReview;

    /**
     * AddMediaOnReviewLoadingObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\AddMediaToReview $addMediaToReview
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\AddMediaToReview $addMediaToReview
    ) {
        $this->addMediaToReview = $addMediaToReview;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getEvent()->getObject();

        if ($review->getEntityId() == $review->getEntityIdByCode('product')) {
            $this->addMediaToReview->execute($review);
        }
    }
}
