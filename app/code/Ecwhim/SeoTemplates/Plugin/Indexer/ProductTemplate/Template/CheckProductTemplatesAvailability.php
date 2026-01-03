<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Template;

use Ecwhim\SeoTemplates\Model\Indexer\ProductTemplate\Template\TemplateProductProcessor;

class CheckProductTemplatesAvailability
{
    /**
     * @var TemplateProductProcessor
     */
    protected $templateProductProcessor;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * CheckProductTemplatesAvailability constructor.
     *
     * @param TemplateProductProcessor $templateProductProcessor
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        TemplateProductProcessor $templateProductProcessor,
        \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory,
        \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->templateProductProcessor = $templateProductProcessor;
        $this->collectionFactory        = $collectionFactory;
        $this->templateRepository       = $templateRepository;
        $this->messageManager           = $messageManager;
    }

    /**
     * @param string $attributeCode
     * @return CheckProductTemplatesAvailability
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(string $attributeCode): CheckProductTemplatesAvailability
    {
        $disabledTemplatesCount = 0;

        /* @var $collection \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\Collection */
        $collection = $this->collectionFactory->create()->addAttributeInConditionFilter($attributeCode);

        foreach ($collection as $template) {
            /* @var $template \Ecwhim\SeoTemplates\Model\ProductTemplate */
            $template->setIsActive(\Ecwhim\SeoTemplates\Model\Source\TemplateStatus::INACTIVE);

            /* @var $template ->getConditions() \Magento\CatalogRule\Model\Rule\Condition\Combine */
            $this->removeAttributeFromConditions($template->getConditions(), $attributeCode);
            $this->templateRepository->save($template);

            $disabledTemplatesCount++;
        }

        if ($disabledTemplatesCount) {
            $this->templateProductProcessor->markIndexerAsInvalid();

            if ($disabledTemplatesCount === 1) {
                $text = 'You disabled %1 Product SEO Template based on "%2" attribute.';
            } else {
                $text = 'You disabled %1 Product SEO Templates based on "%2" attribute.';
            }

            $this->messageManager->addWarningMessage(__($text, $disabledTemplatesCount, $attributeCode));
        }

        return $this;
    }

    /**
     * @param \Magento\CatalogRule\Model\Rule\Condition\Combine $combine
     * @param string $attributeCode
     */
    private function removeAttributeFromConditions(
        \Magento\CatalogRule\Model\Rule\Condition\Combine $combine,
        string $attributeCode
    ): void {
        $conditions = $combine->getConditions();

        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof \Magento\CatalogRule\Model\Rule\Condition\Combine) {
                $this->removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof \Magento\Rule\Model\Condition\Product\AbstractProduct) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }

        $combine->setConditions($conditions);
    }
}
