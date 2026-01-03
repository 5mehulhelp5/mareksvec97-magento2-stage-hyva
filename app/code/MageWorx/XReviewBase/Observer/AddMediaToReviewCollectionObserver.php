<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddMediaToReviewCollectionObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\AddMediaToReviewCollection
     */
    protected $addMediaToReviewCollection;

    /**
     * AddMediaToReviewCollectionObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\AddMediaToReviewCollection $addMediaToReviewCollection
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\AddMediaToReviewCollection $addMediaToReviewCollection
    ) {
        $this->addMediaToReviewCollection = $addMediaToReviewCollection;
    }

    /**
     * Append media to review collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $reviewCollection = $observer->getEvent()->getCollection();

        if ($reviewCollection instanceof \Magento\Review\Model\ResourceModel\Review\Collection) {

            if ($reviewCollection->getFlag('mageworx_need_media')) {
                $this->addMediaToReviewCollection->execute($reviewCollection);
            }
        }
    }
}
