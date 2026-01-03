<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

use Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface;

class CategoryTemplate extends \Magento\Framework\Model\AbstractExtensibleModel
    implements \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = CategoryTemplateManagementInterface::ENTITY_TYPE_CATEGORY_TEMPLATE;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate::class);
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
    public function getApplyToAllCategories(): int
    {
        return (int)$this->getData(self::APPLY_TO_ALL_CATEGORIES);
    }

    /**
     * @inheritDoc
     */
    public function setApplyToAllCategories(int $applyToAllCategories): void
    {
        $this->setData(self::APPLY_TO_ALL_CATEGORIES, $applyToAllCategories);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes(): ?\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateExtensionInterface $extensionAttributes
    ): void {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
