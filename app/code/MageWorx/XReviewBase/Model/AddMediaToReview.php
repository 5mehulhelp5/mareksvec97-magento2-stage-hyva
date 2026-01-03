<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

use MageWorx\XReviewBase\Model\ResourceModel\Media\CollectionFactory as MediaCollectionFactory;

class AddMediaToReview
{
    /**
     * @var MediaCollectionFactory
     */
    protected $mediaCollectionFactory;

    /**
     * AddMediaToReview constructor.
     *
     * @param MediaCollectionFactory $mediaCollectionFactory
     */
    public function __construct(
        MediaCollectionFactory $mediaCollectionFactory
    ) {
        $this->mediaCollectionFactory = $mediaCollectionFactory;
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @param bool $frontendMode
     * @return void
     */
    public function execute($review, $frontendMode = false)
    {
        /** @var \MageWorx\XReviewBase\Model\ResourceModel\Media\Collection $mediaCollection */
        $mediaCollection = $this->mediaCollectionFactory->create();
        $mediaCollection->addFieldToFilter('review_id', $review->getId());

        $data = [];
        foreach ($mediaCollection as $item) {
            $data[$item->getId()] = $frontendMode ? $item : $item->toArray();
        }

        if ($frontendMode) {
            $review->setData('media_gallery', $data);
        } else {
            $images = [
                'images' => $data,
                'values' => []
            ];

            $review->setData('media_gallery', $images);
        }
    }
}
