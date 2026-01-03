<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api\Data;

/**
 * @api
 */
interface ProductTemplateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface[]
     */
    public function getItems();

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
