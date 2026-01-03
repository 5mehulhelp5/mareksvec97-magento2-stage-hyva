<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

class TemplateStoreIdsResolver
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var array|null
     */
    private $allStoreIds;

    /**
     * TemplateStoreIdsResolver constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template
     * @return array
     */
    public function getStoreIds(\Ecwhim\SeoTemplates\Api\Data\TemplateInterface $template): array
    {
        if ($template->getScope() === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_GLOBAL) {
            return [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $template->getStoreIds())) {
            return $this->getAllStoreIds();
        }

        return $template->getStoreIds();
    }

    /**
     * @return array
     */
    private function getAllStoreIds(): array
    {
        if (isset($this->allStoreIds)) {
            return $this->allStoreIds;
        }

        $connection = $this->resource->getConnection();
        $select     = $connection->select();
        $select
            ->from($this->resource->getTableName('store'), 'store_id')
            ->where('store_id != ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->allStoreIds = $connection->fetchCol($select);

        return $this->allStoreIds;
    }
}
