<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class FindStatisticsByReviewCollectionObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\ReviewCollectionStatisticsProvider
     */
    protected $reviewCollectionStatisticsProvider;

    /**
     * FindStatisticsByReviewCollectionObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\ReviewCollectionStatisticsProvider $reviewCollectionStatisticsProvider
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\ReviewCollectionStatisticsProvider $reviewCollectionStatisticsProvider
    ) {
        $this->reviewCollectionStatisticsProvider = $reviewCollectionStatisticsProvider;
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
            $this->reviewCollectionStatisticsProvider->composeStatistics($reviewCollection);
        }
    }
}
