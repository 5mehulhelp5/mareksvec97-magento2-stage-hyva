<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataModifier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\Review;
use MageWorx\ReviewReminderBase\Model\CollectionProvider\Email\ReviewCollectionProvider;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManagerInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\Content\DataModifierInterface;

class AlreadyReviewedProductsRemover implements DataModifierInterface
{
    /**
     * @var ReviewCollectionProvider
     */
    protected $reviewCollectionProvider;

    /**
     * AlreadyReviewedProductsRemover constructor.
     *
     * @param ReviewCollectionProvider $reviewCollectionProvider
     */
    public function __construct(
        ReviewCollectionProvider $reviewCollectionProvider
    ) {
        $this->reviewCollectionProvider = $reviewCollectionProvider;
    }

    /**
     * Remove already reviewed products by customer on specific store from set.
     *
     * @param ContainerManagerInterface $containerManager
     * @return void
     * @throws LocalizedException
     */
    public function modify(ContainerManagerInterface $containerManager): void
    {
        $productIds = $containerManager->getProductIds();
        $storeId    = $containerManager->getStoreId();

        if ($productIds) {

            $currentEmailsData = $containerManager->getCurrentContainers();
            $customerEmails    = [];

            foreach ($currentEmailsData as $emailDataContainer) {
                $customerEmails[] = $emailDataContainer->getCustomerEmail();
            }

            $customerEmails = array_unique($customerEmails);

            if ($customerEmails) {
                $reviewCollection = $this->reviewCollectionProvider->getCollection($storeId, $customerEmails);

                if ($reviewCollection && $reviewCollection->count()) {

                    /** @var DataContainer $emailContainer */
                    foreach ($currentEmailsData as $emailDataContainer) {

                        /** @var Review $item */
                        foreach ($reviewCollection->getItems() as $item) {

                            if ($emailDataContainer->getCustomerEmail() != $item->getEmail()) {
                                continue;
                            }

                            $emailDataContainer->removeProducts([$item->getEntityPkValue()]);
                        }
                    }
                }
            }
        }
    }
}
