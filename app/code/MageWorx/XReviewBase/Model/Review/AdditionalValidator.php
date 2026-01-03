<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Model\Review;

class AdditionalValidator
{
    /**
     * This class can be used for customisation
     *
     * @param \Magento\Review\Model\Review $review
     * @return array|bool
     */
    public function validate($review)
    {
        $errors = [];

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }
}
