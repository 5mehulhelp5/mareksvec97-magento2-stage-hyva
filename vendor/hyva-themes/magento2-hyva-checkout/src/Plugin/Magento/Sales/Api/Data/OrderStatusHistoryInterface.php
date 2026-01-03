<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Sales\Api\Data;

use Magento\Sales\Api\Data\OrderStatusHistoryInterface as Subject;

class OrderStatusHistoryInterface
{
    protected string $commentPrefixOnCustomerComment;

    /**
     * @param string $commentPrefixOnCustomerComment
     */
    public function __construct(
        string $commentPrefixOnCustomerComment = ''
    ) {
        $this->commentPrefixOnCustomerComment = $commentPrefixOnCustomerComment;
    }

    /**
     * @param Subject $subject
     * @param $comment
     * @return mixed|string
     */
    public function afterGetComment(Subject $subject, $comment)
    {
        if ($subject->getData('is_customer_comment')) {
            return sprintf('%s: %s', __($this->commentPrefixOnCustomerComment), $comment);
        }

        return $comment;
    }
}
