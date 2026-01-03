<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataModifier\Email;

use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory;
use Magento\Review\Model\Review;
use MageWorx\ReviewReminderBase\Model\CollectionProvider\Email\ReviewCollectionProvider;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManagerInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\Content\DataModifierInterface;

class ExampleReviewFiller implements DataModifierInterface
{
    const REVIEW_COUNT = 2;

    /**
     * @var ReviewCollectionProvider
     */
    protected $reviewCollectionProvider;

    /**
     * @var array
     */
    protected $reviews = [];

    /**
     * @var CollectionFactory
     */
    protected $ratingVoteCollectionFactory;

    /**
     * AlreadyReviewedProductsRemover constructor.
     *
     * @param ReviewCollectionProvider $reviewCollectionProvider
     * @param CollectionFactory $ratingVoteCollectionFactory
     */
    public function __construct(
        ReviewCollectionProvider $reviewCollectionProvider,
        CollectionFactory $ratingVoteCollectionFactory
    ) {
        $this->reviewCollectionProvider    = $reviewCollectionProvider;
        $this->ratingVoteCollectionFactory = $ratingVoteCollectionFactory;
    }

    /**
     * Add two additional review to email data container
     *
     * @param ContainerManagerInterface $containerManager
     * @return bool|void
     * @throws LocalizedException
     */
    public function modify(ContainerManagerInterface $containerManager): void
    {
        $productIds = $containerManager->getProductIds();
        $storeId    = $containerManager->getStoreId();

        if ($productIds) {

            foreach ($productIds as $productId) {
                $reviewCollection = $this->reviewCollectionProvider->getRandomReviewCollection(
                    $storeId,
                    $productId,
                    self::REVIEW_COUNT
                );

                $this->reviews[$productId] = array_values($reviewCollection->getItems());
            }

            $currentEmailsData = $containerManager->getCurrentContainers();

            /** @var DataContainer $emailDataContainer */
            foreach ($currentEmailsData as $emailDataContainer) {

                $containerReviews    = [];
                $containerProductIds = $emailDataContainer->getProductIds();
                $count               = 0;

                for ($i = 0; $i < self::REVIEW_COUNT; $i++) {
                    foreach ($containerProductIds as $productId) {

                        if (!empty($this->reviews[$productId][$i])) {

                            $containerReviews[] = $this->reviews[$productId][$i];
                            $count++;
                            if ($count == self::REVIEW_COUNT) {
                                break 2;
                            }
                        }
                    }
                }

                if ($containerReviews) {
                    foreach ($containerReviews as $reviewItem) {
                        $this->addRating($reviewItem);
                    }
                }

                $emailDataContainer->setReviews($containerReviews);
            }
        }
    }

    /**
     * @param Review $review
     * @return void
     */
    protected function addRating($reviewItem)
    {
        $collection = $this->ratingVoteCollectionFactory->create();

        $collection
            ->addFieldToFilter('review_id', $reviewItem->getReviewId())
            ->addOrder('rating_id', 'ASC');

        $collectionData = $collection->getData();

        if (empty($collectionData)) {
            $ratingValue = 0;
        } else {
            $count = count($collectionData);

            $ratingValue = 0;

            foreach ($collectionData as $ratingDatum) {
                $ratingValue += $ratingDatum['percent'];
            }

            $ratingValue = ceil($ratingValue / $count);
        }

        $reviewItem->setRating($ratingValue);
    }
}
