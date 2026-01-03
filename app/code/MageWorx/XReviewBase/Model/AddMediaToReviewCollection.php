<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

use MageWorx\XReviewBase\Model\ResourceModel\Media\CollectionFactory as MediaCollectionFactory;

class AddMediaToReviewCollection
{
    /**
     * @var MediaCollectionFactory
     */
    protected $mediaCollectionFactory;

    /**
     * AddMediaToReviewCollection constructor.
     *
     * @param MediaCollectionFactory $mediaCollectionFactory
     */
    public function __construct(
        MediaCollectionFactory $mediaCollectionFactory
    ) {
        $this->mediaCollectionFactory = $mediaCollectionFactory;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $reviewCollection
     * @return void
     */
    public function execute($reviewCollection)
    {
        if (!$reviewCollection->hasFlag('media_added')) {

            $entityIds = $reviewCollection->getColumnValues('review_id');

            if (count($entityIds) === 0) {
                return $this;
            }

            /** @var \MageWorx\XReviewBase\Model\ResourceModel\Media\Collection $mediaCollection */
            $mediaCollection = $this->mediaCollectionFactory->create();
            $mediaCollection->addFieldToFilter('review_id', ['in' => $entityIds]);
            $mediaCollection->addFieldToFilter('disabled', 0);
            $mediaCollection->setOrder('review_id')->addOrder('position');

            $data = [];

            /** @var \MageWorx\XReviewBase\Model\Media $item */
            foreach ($mediaCollection as $item) {
                if (empty($data[$item->getReviewId()])) {
                    $data[$item->getReviewId()] = [];
                }

                $data[$item->getReviewId()][$item->getId()] = $item;
            }

            foreach ($reviewCollection as $review) {
                if (!empty($data[$review->getId()])) {
                    $review->setData('media_gallery', $data[$review->getId()]);
                } else {
                    $review->setData('media_gallery', []);
                }
            }

            $reviewCollection->setFlag('media_added', true);
        }
    }
}
