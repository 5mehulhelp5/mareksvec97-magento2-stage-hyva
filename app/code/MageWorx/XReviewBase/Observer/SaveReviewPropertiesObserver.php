<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Observer;

use MageWorx\XReviewBase\Model\ResourceModel\Review as ReviewResource;

class SaveReviewPropertiesObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var ReviewResource
     */
    protected $reviewResource;

    /**
     * SaveReviewPropertiesObserver constructor.
     *
     * @param ReviewResource $reviewResource
     */
    public function __construct(
        ReviewResource $reviewResource
    ) {
        $this->reviewResource = $reviewResource;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\Review $review */
        $review = $observer->getEvent()->getObject();

        if ($review) {
            $this->reviewResource->saveReviewDetailFields($review);
        }
    }
}
