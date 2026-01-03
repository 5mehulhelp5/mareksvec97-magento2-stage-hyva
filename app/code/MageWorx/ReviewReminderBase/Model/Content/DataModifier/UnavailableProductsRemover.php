<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataModifier;

use MageWorx\ReviewReminderBase\Model\CollectionProvider\Email\ProductCollectionProvider;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManagerInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\Content\DataModifierInterface;

class UnavailableProductsRemover implements DataModifierInterface
{
    /**
     * @var ProductCollectionProvider
     */
    protected $productCollectionProvider;

    /**
     * AvailabilityModifier constructor.
     *
     * @param ProductCollectionProvider $productCollectionProvider
     */
    public function __construct(
        ProductCollectionProvider $productCollectionProvider
    ) {
        $this->productCollectionProvider = $productCollectionProvider;
    }

    /**
     * @param ContainerManagerInterface $containerManager
     * @return bool|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modify(ContainerManagerInterface $containerManager): void
    {
        $productIds = $containerManager->getProductIds();
        $storeId    = $containerManager->getStoreId();

        if ($productIds) {
            $productCollection = $this->productCollectionProvider->getCollection($storeId, $productIds);
            $validProductIds   = $productCollection->getLoadedIds();

            /** @var DataContainer $emailContainer */
            foreach ($containerManager->getCurrentContainers() as $emailDataContainer) {
                $emailDataContainer->removeProducts($validProductIds, true);
            }
        }
    }
}
