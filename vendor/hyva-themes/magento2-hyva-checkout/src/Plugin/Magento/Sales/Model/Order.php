<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Sales\Model;

use Magento\Sales\Model\Order as Subject;
use Magento\Sales\Model\ResourceModel\Order\Status\History\Collection;

class Order
{
    public function afterGetAllStatusHistory(Subject $subject, array $history): array
    {
        return $this->applyIsCustomerCommentPrefixes($history, 'Customer wrote');
    }

    public function afterGetStatusHistoryCollection(Subject $subject, Collection $history): Collection
    {
        $items = $history->getItems();
        $history->removeAllItems();

        return $history->setItems($this->applyIsCustomerCommentPrefixes($items, 'Customer wrote'));
    }

    public function afterGetVisibleStatusHistory(Subject $subject, array $history): array
    {
        return $this->applyIsCustomerCommentPrefixes($history, 'Me');
    }

    public function applyIsCustomerCommentPrefixes(array $history, string $prefix)
    {
        foreach ($history as $item) {
            if ($item->getIsCustomerComment()) {
                $item->setComment(sprintf('%s: %s', __($prefix), $item->getComment()));
            }
        }

        return $history;
    }
}
