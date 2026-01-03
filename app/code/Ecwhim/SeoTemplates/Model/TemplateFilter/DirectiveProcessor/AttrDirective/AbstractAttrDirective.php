<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor\AttrDirective;

use Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor\DirectiveProcessorInterface;

abstract class AbstractAttrDirective implements DirectiveProcessorInterface
{
    /**
     * @var array|null
     */
    protected $data;

    /**
     * @param array $construction
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilter $filter
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process(array $construction, \Ecwhim\SeoTemplates\Model\TemplateFilter $filter): string
    {
        $attributeCode = trim($construction[1]);

        if (!$this->validateAttributeCode($attributeCode)) {
            return '';
        }

        if ($this->data === null) {
            $this->prepareData($filter);
            $filter->addAfterMassFilterCallback([$this, 'resetData']);
        }

        $entityId = $filter->getCurrentEntityId();

        if (empty($this->data[$entityId])) {
            return '';
        }

        return $this->getAttributeValue($attributeCode, $this->data[$entityId], $filter->getStoreId());
    }

    /**
     * @inheritDoc
     */
    public function getRegularExpression(): string
    {
        return '/{{attr\s*(.*?)}}/si';
    }

    /**
     * @return AbstractAttrDirective
     */
    public function resetData(): AbstractAttrDirective
    {
        $this->data = null;

        return $this;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilter $filter
     */
    abstract protected function prepareData(\Ecwhim\SeoTemplates\Model\TemplateFilter $filter): void;

    /**
     * @param string $attributeCode
     * @param array $entityData
     * @param int $storeId
     * @return string
     */
    abstract protected function getAttributeValue(string $attributeCode, array $entityData, int $storeId): string;

    /**
     * @param string $attributeCode
     * @return bool
     */
    protected function validateAttributeCode(string $attributeCode): bool
    {
        return !empty($attributeCode);
    }

    /**
     * @param string $content
     * @return array
     */
    protected function getAttributeCodes(string $content): array
    {
        $codes = [];

        if (preg_match_all($this->getRegularExpression(), $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $codes[] = trim($match[1]);
            }
        }

        return $codes;
    }
}
