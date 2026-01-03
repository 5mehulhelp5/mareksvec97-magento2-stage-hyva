<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class AddColumnsToReviewGridObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Block\Adminhtml\Grid $grid */
        $grid = $observer->getEvent()->getGrid();

        if ($grid instanceof \Magento\Review\Block\Adminhtml\Grid) {

            $grid->addColumnAfter(
                'answer',
                [
                    'header' => __('Answer'),
                    'type' => 'text',
                    'filter_index' => 'rdt.answer',
                    'index' => 'answer'
                ],
                'sku'
            );

            $grid->addColumnAfter(
            'is_recommend',
                [
                    'header' => __('Is Recommend'),
                    'type' => 'options',
                    'options' => [0 => __('No'), 1 => __('Yes')],
                    'filter_index' => 'rdt.is_recommend',
                    'index' => 'is_recommend'
                ],
                'answer'
            );

            $grid->addColumnAfter(
            'is_verified',
                [
                    'header' => __('Is Verified'),
                    'type' => 'options',
                    'options' => [0 => __('No'), 1 => __('Yes')],
                    'filter_index' => 'rdt.is_verified',
                    'index' => 'is_verified'
                ],
                'is_recommend'
            );

            $grid->sortColumnsByOrder();
        }
    }
}
