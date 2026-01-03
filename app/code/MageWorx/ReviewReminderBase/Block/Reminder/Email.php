<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Block\Reminder;

use Magento\Framework\View\Element\Template;
use Magento\Review\Model\Review;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\Component\Product;

class Email extends Template
{
    const MAX_REVIEW_DETAIL_LENGTH = 100;

    /**
     * @param Review $review
     * @return string
     */
    public function getProductName($review): string
    {
        /** @var Product $product */
        foreach ($this->getProducts() as $orderId => $orderData) {

            foreach ($orderData as $productId => $product) {
                if ($review->getEntityPkValue() == $product->getProductId()) {
                    return $product->getName();
                }
            }
        }

        return '';
    }

    /**
     * @param Review $review
     * @return string
     */
    public function getReviewDetail($review): string
    {
        $suffix = mb_strlen($review->getDetail()) > self::MAX_REVIEW_DETAIL_LENGTH ? '...' : '';

        return mb_substr($review->getDetail(), 0, self::MAX_REVIEW_DETAIL_LENGTH) . $suffix;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getRatingImageUrlForProduct($product)
    {
        return $this->getRatingImageUrl($product->getRatingSummary() * 5 / 100);
    }

    /**
     * @param Review $review
     * @return string
     */
    public function getRatingImageUrlForReview($review)
    {
        return $this->getRatingImageUrl($review->getRating() * 5 / 100);
    }

    /**
     * @param int $rating
     * @return string
     */
    public function getRatingImageUrl($rating): string
    {
        if ($rating > 4.5 && $rating <= 4.75) {
            $image = 'stars-4-75.png';
        } else {
            $image = 'stars-' . number_format(ceil($rating / 0.5) * 0.5, 1, '-', '') . '.png';
        }

        return $this->_assetRepo->getUrl('MageWorx_ReviewReminderBase::images/rating/' . $image);
    }
}
