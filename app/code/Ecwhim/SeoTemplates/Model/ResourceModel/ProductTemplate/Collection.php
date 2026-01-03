<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;
use Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface;
use Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateStoreTable;

class Collection extends \Ecwhim\SeoTemplates\Model\ResourceModel\AbstractTemplateCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = ProductTemplateInterface::TEMPLATE_ID;

    /**
     * @var string
     */
    protected $_eventObject = 'template_collection';

    /**
     * @var string
     */
    protected $_eventPrefix = ProductTemplateManagementInterface::ENTITY_TYPE_PRODUCT_TEMPLATE . '_collection';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(
            \Ecwhim\SeoTemplates\Model\ProductTemplate::class,
            \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate::class
        );
        $this->_map['fields'][self::STORE_FIELD] = self::STORE_TABLE_ALIAS .
            '.' . AddProductTemplateStoreTable::COLUMN_STORE_ID;
    }

    /**
     * @param string $attributeCode
     * @return $this
     * @api
     */
    public function addAttributeInConditionFilter(string $attributeCode)
    {
        $match = sprintf('%%%s%%', substr($this->jsonSerializer->serialize(['attribute' => $attributeCode]), 1, -1));

        $this->addFieldToFilter(
            \Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateTable::COLUMN_CONDITIONS_SERIALIZED,
            ['like' => $match]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        $this->addRelatedStoreIds(
            AddProductTemplateStoreTable::TABLE_PRODUCT_TEMPLATE_STORE,
            ProductTemplateInterface::TEMPLATE_ID,
            AddProductTemplateStoreTable::COLUMN_STORE_ID
        );

        return parent::_afterLoad();
    }

    /**
     * @inheritDoc
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable(
            AddProductTemplateStoreTable::TABLE_PRODUCT_TEMPLATE_STORE,
            ProductTemplateInterface::TEMPLATE_ID
        );

        parent::_renderFiltersBefore();
    }
}
