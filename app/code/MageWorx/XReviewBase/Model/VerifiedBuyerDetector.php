<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class VerifiedBuyerDetector
{
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * LocationTextCreator constructor.
     *
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function execute(int $customerId, int $productId): bool
    {
        $collection = $this->orderCollectionFactory
            ->create($customerId)
            ->addFieldToFilter('status', 'complete');

        $select = $collection->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);

        $collection
            ->join(
                ['s_o_i' => $collection->getTable('sales_order_item')],
                'main_table.entity_id = s_o_i.order_id',
                ['sku']
            );

        $select
            ->where('product_id = ?', $productId)
            ->group('s_o_i.product_id');

        return (bool) $collection->getConnection()->fetchRow($select);
    }
}
