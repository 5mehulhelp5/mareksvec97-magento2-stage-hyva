<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api\Data;

/**
 * @api
 */
interface CategoryTemplateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface[]
     */
    public function getItems();

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
