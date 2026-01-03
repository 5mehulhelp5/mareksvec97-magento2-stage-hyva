<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataModifier;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\ReviewReminderBase\Model\CollectionProvider\Email\ProductCollectionProvider;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManagerInterface;
use MageWorx\ReviewReminderBase\Model\Content\Converter\Component\ComponentProductConverter;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\Content\DataModifierInterface;

class AdditionalProductDataFiller implements DataModifierInterface
{
    /**
     * @var ProductCollectionProvider
     */
    protected $productCollectionProvider;

    /**
     * @var ComponentProductConverter
     */
    protected $productConverter;

    /**
     * AvailabilityModifier constructor.
     *
     * @param ProductCollectionProvider $productCollectionProvider
     * @param ComponentProductConverter $productConverter
     */
    public function __construct(
        ProductCollectionProvider $productCollectionProvider,
        ComponentProductConverter $productConverter
    ) {
        $this->productCollectionProvider = $productCollectionProvider;
        $this->productConverter          = $productConverter;
    }

    /**
     * Adds some additional properties such as aggregate rating, URL, etc.
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

            $productCollection = $this->productCollectionProvider->getCollection($storeId, $productIds);

            /** @var DataContainer $emailContainer */
            foreach ($containerManager->getCurrentContainers() as $emailDataContainer) {

                foreach ($emailDataContainer->getOrderProducts() as $orderId => $order) {

                    foreach ($order as $productId => $product) {
                        if ($newProduct = $productCollection->getItemById($productId)) {
                            $this->productConverter->fillComponentProductByProduct($newProduct, $product);
                        }
                    }
                }
            }
        }
    }
}
