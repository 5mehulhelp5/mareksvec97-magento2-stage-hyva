<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

use Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor\DirectiveProcessorInterface;

class TemplateFilter implements TemplateFilterInterface
{
    const TEMPLATE_VAR_DIRECTIVE_GROUP = 'template_var';
    const EXPRESSION_DIRECTIVE_GROUP   = 'expression';
    const ENTITY_VAR_DIRECTIVE_GROUP   = 'entity_var';

    /**
     * @var \Ecwhim\SeoTemplates\Model\TemplateFilter\ResultValueResolverFactory
     */
    private $resultValueResolverFactory;

    /**
     * @var DirectiveProcessorInterface[]
     */
    private $directiveProcessors;

    /**
     * @var array
     */
    private $afterMassFilterCallbacks = [];

    /**
     * @var array|null
     */
    private $entityIds;

    /**
     * @var string|null
     */
    private $templateContent;

    /**
     * @var int|null
     */
    private $storeId;

    /**
     * @var int|null
     */
    private $currentEntityId;

    /**
     * TemplateFilter constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilter\ResultValueResolverFactory $resultValueResolverFactory
     * @param array $directiveProcessors
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\TemplateFilter\ResultValueResolverFactory $resultValueResolverFactory,
        array $directiveProcessors = []
    ) {
        $this->resultValueResolverFactory = $resultValueResolverFactory;
        $this->directiveProcessors        = $directiveProcessors;
    }

    /**
     * @inheritDoc
     */
    public function massFilter(array $entityIds, string $content, int $storeId, string $type): array
    {
        $this->setEntityIds($entityIds);
        $this->setTemplateContent($content);
        $this->setStoreId($storeId);

        $resultValueResolver = $this->resultValueResolverFactory->get($type);
        $content             = $this->filter($content, self::TEMPLATE_VAR_DIRECTIVE_GROUP);
        $values              = [];

        foreach ($entityIds as $entityId) {
            $this->setCurrentEntityId((int)$entityId);

            $value             = $this->filter($content, self::EXPRESSION_DIRECTIVE_GROUP);
            $value             = $this->filterUsingEntityVarDirectiveGroup($value);
            $values[$entityId] = $resultValueResolver->resolve($value);
        }

        $values = $this->afterMassFilter($values);

        return $values;
    }

    /**
     * @param string $content
     * @return string
     */
    public function filterUsingEntityVarDirectiveGroup(string $content): string
    {
        return $this->filter($content, self::ENTITY_VAR_DIRECTIVE_GROUP);
    }

    /**
     * @return string
     */
    public function getVarRegularExpression(): string
    {
        return '/{{[a-z]{1,10}\s+.+?}}/si';
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityIds(): array
    {
        if ($this->entityIds === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Entity IDs is not specified.'));
        }

        return $this->entityIds;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTemplateContent(): string
    {
        if ($this->templateContent === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Template Content is not specified.'));
        }

        return $this->templateContent;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoreId(): int
    {
        if ($this->storeId === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Store ID is not specified.'));
        }

        return $this->storeId;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentEntityId(): int
    {
        if ($this->currentEntityId === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Current Entity ID is not specified.'));
        }

        return $this->currentEntityId;
    }

    /**
     * @param callable $afterMassFilterCallback
     * @return TemplateFilter
     */
    public function addAfterMassFilterCallback(callable $afterMassFilterCallback): TemplateFilter
    {
        if (in_array($afterMassFilterCallback, $this->afterMassFilterCallbacks)) {
            return $this;
        }

        $this->afterMassFilterCallbacks[] = $afterMassFilterCallback;

        return $this;
    }

    /**
     * @param string $content
     * @param string $directiveGroup
     * @return string
     */
    private function filter(string $content, string $directiveGroup): string
    {
        if (empty($this->directiveProcessors[$directiveGroup])) {
            return $content;
        }

        foreach ($this->directiveProcessors[$directiveGroup] as $directiveProcessor) {
            if (!$directiveProcessor instanceof DirectiveProcessorInterface) {
                throw new \InvalidArgumentException(
                    'Directive processors must implement ' . DirectiveProcessorInterface::class
                );
            }

            if (preg_match_all($directiveProcessor->getRegularExpression(), $content, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    $replacedValue = $directiveProcessor->process($construction, $this);

                    $content = str_replace($construction[0], $replacedValue, $content);
                }
            }
        }

        return $content;
    }

    /**
     * @param string[] $values
     * @return string[]
     */
    private function afterMassFilter(array $values): array
    {
        foreach ($this->afterMassFilterCallbacks as $callback) {
            $result = call_user_func($callback, $values);

            if (is_array($result)) {
                $values = $result;
            }
        }

        $this->resetAfterMassFilterCallbacks();

        return $values;
    }

    /**
     * @return void
     */
    private function resetAfterMassFilterCallbacks(): void
    {
        $this->afterMassFilterCallbacks = [];
    }

    /**
     * @param array $entityIds
     */
    private function setEntityIds(array $entityIds): void
    {
        $this->entityIds = $entityIds;
    }

    /**
     * @param string $content
     */
    private function setTemplateContent(string $content): void
    {
        $this->templateContent = $content;
    }

    /**
     * @param int $storeId
     */
    private function setStoreId(int $storeId): void
    {
        $this->storeId = $storeId;
    }

    /**
     * @param int $currentEntityId
     */
    private function setCurrentEntityId(int $currentEntityId): void
    {
        $this->currentEntityId = $currentEntityId;
    }
}
