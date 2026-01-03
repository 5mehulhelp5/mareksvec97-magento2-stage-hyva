<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Plugin;

class ReviewValidatePlugin
{
    /**
     * @var \MageWorx\XReview\Model\Review\AdditionalValidator
     */
    protected $additionalValidator;

    public function __construct(
        \MageWorx\XReviewBase\Model\Review\AdditionalValidator $additionalValidator
    ) {
        $this->additionalValidator = $additionalValidator;
    }

    /**
     * @param \Magento\Review\Model\Review $subject
     * @param array|bool $result
     * @return array
     */
    public function afterValidate($subject, $result)
    {
        $additionalResult = $this->additionalValidator->validate($subject);

        if (is_array($additionalResult)) {
            $result = array_merge(is_array($result) ? $result : [], $additionalResult);
        }

        return $result;
    }
}
