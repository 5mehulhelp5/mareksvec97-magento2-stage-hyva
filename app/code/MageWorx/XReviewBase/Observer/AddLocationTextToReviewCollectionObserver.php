<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddLocationTextToReviewCollectionObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\AddLocationTextToReviewCollection
     */
    protected $addLocationTextToReviewCollection;

    /**
     * AddLocationTextToReviewCollectionObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\AddLocationTextToReviewCollection $addLocationTextToReviewCollection
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\AddLocationTextToReviewCollection $addLocationTextToReviewCollection
    ) {
        $this->addLocationTextToReviewCollection = $addLocationTextToReviewCollection;
    }

    /**
     * Append location text to review collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $reviewCollection = $observer->getEvent()->getCollection();

        if ($reviewCollection instanceof \Magento\Review\Model\ResourceModel\Review\Collection) {

            if ($reviewCollection->getFlag('mageworx_need_location_text')) {
                $this->addLocationTextToReviewCollection->execute($reviewCollection);
            }
        }
    }
}
