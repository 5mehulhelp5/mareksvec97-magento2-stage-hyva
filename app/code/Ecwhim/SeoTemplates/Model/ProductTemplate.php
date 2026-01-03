<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

use Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface;
use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class ProductTemplate extends \Magento\Rule\Model\AbstractModel implements ProductTemplateInterface
{
    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    protected $combineConditionFactory;

    /**
     * @var \Magento\Rule\Model\Action\CollectionFactory
     */
    protected $actionCollectionFactory;

    /**
     * @var \Magento\CatalogRule\Model\Data\Condition\Converter
     */
    protected $conditionConverter;

    /**
     * @var string
     */
    protected $_eventPrefix = ProductTemplateManagementInterface::ENTITY_TYPE_PRODUCT_TEMPLATE;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $combineConditionFactory,
        \Magento\Rule\Model\Action\CollectionFactory $actionCollectionFactory,
        \Magento\CatalogRule\Model\Data\Condition\Converter $conditionConverter,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory = null,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );

        $this->combineConditionFactory = $combineConditionFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        $this->conditionConverter      = $conditionConverter;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate::class);
    }

    /**
     * @return \Magento\CatalogRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineConditionFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function getTemplateId(): ?int
    {
        $id = $this->getData(self::TEMPLATE_ID);

        return $id ? (int)$id : null;
    }

    /**
     * @inheritDoc
     */
    public function setTemplateId(int $id): void
    {
        $this->setData(self::TEMPLATE_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): void
    {
        $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive(): int
    {
        return (int)$this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(int $isActive): void
    {
        $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritDoc
     */
    public function getScope(): string
    {
        return (string)$this->getData(self::SCOPE);
    }

    /**
     * @inheritDoc
     */
    public function setScope(string $scope): void
    {
        $this->setData(self::SCOPE, $scope);
    }

    /**
     * @inheritDoc
     */
    public function getStoreIds(): array
    {
        $storeIds = $this->getData(self::STORE_IDS);

        return is_array($storeIds) ? $storeIds : [];
    }

    /**
     * @inheritDoc
     */
    public function setStoreIds(array $storeIds): void
    {
        $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string)$this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): void
    {
        $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return (string)$this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent(string $content): void
    {
        $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritDoc
     */
    public function getApplyByCron(): int
    {
        return (int)$this->getData(self::APPLY_BY_CRON);
    }

    /**
     * @inheritDoc
     */
    public function setApplyByCron(int $applyByCron): void
    {
        $this->setData(self::APPLY_BY_CRON, $applyByCron);
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return (int)$this->getData(self::PRIORITY);
    }

    /**
     * @inheritDoc
     */
    public function setPriority(int $priority): void
    {
        $this->setData(self::PRIORITY, $priority);
    }

    /**
     * @inheritDoc
     */
    public function getApplicationTime(): string
    {
        return (string)$this->getData(self::APPLICATION_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setApplicationTime(string $applicationTime): void
    {
        $this->setData(self::APPLICATION_TIME, $applicationTime);
    }

    /**
     * @inheritDoc
     */
    public function getTemplateCondition(): ?\Magento\CatalogRule\Api\Data\ConditionInterface
    {
        return $this->conditionConverter->arrayToDataModel($this->getConditions()->asArray());
    }

    /**
     * @inheritDoc
     */
    public function setTemplateCondition(\Magento\CatalogRule\Api\Data\ConditionInterface $condition): void
    {
        $combineCondition = $this->getConditions();
        $combineCondition
            ->setConditions([])
            ->loadArray($this->conditionConverter->dataModelToArray($condition));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes(): ?\Ecwhim\SeoTemplates\Api\Data\ProductTemplateExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Ecwhim\SeoTemplates\Api\Data\ProductTemplateExtensionInterface $extensionAttributes
    ): void {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
