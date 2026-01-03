<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddFieldsToProductReviewCollectionObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\ResourceModel\Review
     */
    protected $reviewResource;

    /**
     * @var \MageWorx\XReviewBase\Model\Review\AdditionalDetailsFields\Config
     */
    protected $additionalDetailsFieldsConfig;

    /**
     * AddFieldsToReviewCollectionObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\ResourceModel\Review $reviewResource
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\ResourceModel\Review $reviewResource,
        \MageWorx\XReviewBase\Model\Review\AdditionalDetailsFields\Config $additionalDetailsFieldsConfig
    ) {
        $this->reviewResource = $reviewResource;
        $this->additionalDetailsFieldsConfig = $additionalDetailsFieldsConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Model\ResourceModel\Review\Product\Collection $collection */
        $collection = $observer->getEvent()->getCollection();

        if ($collection instanceof \Magento\Review\Model\ResourceModel\Review\Product\Collection) {
            $from = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
            if (!empty($from['rdt'])) {
                $fields = [];

                foreach ($this->additionalDetailsFieldsConfig->getFieldsForReviewDetail() as $field) {
                    $fields[] = 'rdt.' . $field;
                }

                if ($fields) {
                    $collection->getSelect()->columns($fields);
                }
            }
        }
    }
}
