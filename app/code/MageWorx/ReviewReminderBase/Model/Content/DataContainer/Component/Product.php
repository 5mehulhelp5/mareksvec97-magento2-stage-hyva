<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataContainer\Component;

use Magento\Framework\DataObject;

class Product extends DataObject
{
    const PRODUCT_ID = 'product_id';

    const SALES_SKU = 'sales_sku';

    const SKU = 'sku';

    const NAME = 'name';

    const URL = 'url';

    const IMAGE_URL = 'image_url';

    const REVIEW_COUNT = 'review_count';

    const RATING_SUMMARY = 'rating_summary';

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }

    /**
     * @return string|null
     */
    public function getSalesSku(): ?string
    {
        return $this->getData(self::SALES_SKU);
    }

    /**
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->getData(self::SKU);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->getData(self::URL);
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->getData(self::IMAGE_URL);
    }

    /**
     * @return int|null
     */
    public function getReviewCount(): ?int
    {
        return (int)$this->getData(self::REVIEW_COUNT);
    }

    /**
     * @return int|null
     */
    public function getRatingSummary(): ?int
    {
        return (int)$this->getData(self::RATING_SUMMARY);
    }
}
