<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataModifier\Popup;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewReminderBase\Model\CollectionProvider\Email\ProductCollectionProvider;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManagerInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\Content\DataModifierInterface;
use MageWorx\ReviewReminderBase\Model\ReminderConfigReader;

class NeedlessProductsRemover implements DataModifierInterface
{
    /**
     * @var ProductCollectionProvider
     */
    protected $productCollectionProvider;

    /**
     * @var ReminderConfigReader
     */
    protected $configReader;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * NeedlessProductsRemover constructor.
     *
     * @param ProductCollectionProvider $productCollectionProvider
     * @param ReminderConfigReader $configReader
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductCollectionProvider $productCollectionProvider,
        ReminderConfigReader $configReader,
        StoreManagerInterface $storeManager
    ) {
        $this->productCollectionProvider = $productCollectionProvider;
        $this->configReader              = $configReader;
        $this->storeManager              = $storeManager;
    }

    /**
     * @param ContainerManagerInterface $containerManager
     * @return bool|void
     * @throws NoSuchEntityException
     */
    public function modify(ContainerManagerInterface $containerManager): void
    {
        $storeId  = (int)$this->storeManager->getStore()->getId();
        $maxCount = $this->configReader->getMaxProductsCountInPopup($storeId);

        /** @var DataContainer $emailContainer */
        foreach ($containerManager->getCurrentContainers() as $emailDataContainer) {
            $productIds = array_slice(array_unique($emailDataContainer->getProductIds()), $maxCount);

            if ($productIds) {
                $emailDataContainer->removeProducts($productIds);
                $emailDataContainer->removeDuplicateProducts();
            }
        }
    }
}
