<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Model\CollectionProvider\Email;

use Exception;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewColletcion;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;

class ReviewCollectionProvider
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var ReviewCollectionFactory
     */
    protected $reviewCollectionFactory;

    /**
     * @var ProductCollectionProvider
     */
    protected $productCollectionProvider;

    /**
     * @var array
     */
    protected $collectionCache = [];

    /**
     * ReviewCollectionProvider constructor.
     *
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param ProductCollectionProvider $productCollectionProvider
     * @param EventManager $eventManager
     */
    public function __construct(
        ReviewCollectionFactory $reviewCollectionFactory,
        ProductCollectionProvider $productCollectionProvider,
        EventManager $eventManager
    ) {
        $this->eventManager              = $eventManager;
        $this->reviewCollectionFactory   = $reviewCollectionFactory;
        $this->productCollectionProvider = $productCollectionProvider;
    }

    /**
     * This collection is needed for excluding reviewed products.
     * The $customerIds param is needed only for first load. Then the collection will be retrieve from cache.
     *
     * @param int $storeId
     * @param array $customerEmails
     * @return ReviewColletcion|null
     * @throws LocalizedException
     */
    public function getCollection($storeId, $customerEmails)
    {
        if (!array_key_exists($storeId, $this->collectionCache)) {

            $productCollection = $this->productCollectionProvider->getCollection($storeId);
            $productIds        = $productCollection ? $productCollection->getLoadedIds() : [];

            if ($customerEmails && $productIds) {

                /** @var  ReviewColletcion $reviewCollection */
                $reviewCollection = $this->reviewCollectionFactory->create();

                $reviewCollection
                    ->addStoreFilter($storeId)
                    ->addFieldToFilter('entity_code', 'product')
                    ->addFieldToFilter('status_id', ['nin' => Review::STATUS_NOT_APPROVED])
                    ->addFieldToFilter('entity_pk_value', ['in' => $productIds])
                    ->join(
                        $reviewCollection->getTable('review_entity'),
                        'main_table.entity_id=' . $reviewCollection->getTable('review_entity') . '.entity_id',
                        ['entity_code']
                    )
                    ->join(
                        $reviewCollection->getTable('customer_entity'),
                        'customer_id=' . $reviewCollection->getTable('customer_entity') . '.entity_id',
                        ['email']
                    )
                    ->getSelect()
                    ->where('email IN(?)', $customerEmails);

                $this->eventManager->dispatch(
                    'mageworx_reviewreminderbase_email_review_collection_load_before',
                    [
                        'collection'     => $reviewCollection,
                        'productIds'     => $productIds,
                        'customerEmails' => $customerEmails
                    ]
                );

                if ($reviewCollection->isLoaded()) {
                    throw new Exception('Incorrect state: review collection already loaded');
                }

                $reviewCollection->load();

                $this->eventManager->dispatch(
                    'mageworx_reviewreminderbase_email_review_collection_load_after',
                    ['collection' => $reviewCollection]
                );
            } else {
                $reviewCollection = null;
            }

            $this->collectionCache[$storeId] = $reviewCollection;
        }

        return $this->collectionCache[$storeId];
    }

    /**
     * @param int $storeId
     * @param int $productId
     * @param int $limit
     * @return ReviewColletcion
     */
    public function getRandomReviewCollection($storeId, $productId, $limit)
    {
        /** @var  ReviewColletcion $reviewCollection */
        $reviewCollection = $this->reviewCollectionFactory->create();

        $reviewCollection
            ->addStoreFilter($storeId)
            ->addFieldToFilter('entity_code', 'product')
            ->addFieldToFilter('status_id', ['in' => Review::STATUS_APPROVED])
            ->addFieldToFilter('entity_pk_value', ['in' => $productId])
            ->join(
                $reviewCollection->getTable('review_entity'),
                'main_table.entity_id=' . $reviewCollection->getTable('review_entity') . '.entity_id',
                ['entity_code']
            )
            ->setPageSize($limit)
            ->setCurPage(1)
            ->getSelect()->order(new \Zend_Db_Expr('RAND()'));

        $this->eventManager->dispatch(
            'mageworx_reviewreminderbase_email_review_collection_random_load_before',
            ['collection' => $reviewCollection, 'productId' => $productId]
        );

        $reviewCollection->load();

        $this->eventManager->dispatch(
            'mageworx_reviewreminderbase_email_review_collection_random_load_after',
            ['collection' => $reviewCollection]
        );

        return $reviewCollection;
    }
}
