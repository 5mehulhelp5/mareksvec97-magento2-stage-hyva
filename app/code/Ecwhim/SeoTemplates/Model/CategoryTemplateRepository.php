<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

class CategoryTemplateRepository implements \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterfaceFactory
     */
    protected $templateFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate
     */
    protected $templateResource;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface[]
     */
    protected $instances = [];

    /**
     * CategoryTemplateRepository constructor.
     *
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterfaceFactory $templateFactory
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterfaceFactory $templateFactory,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->templateFactory      = $templateFactory;
        $this->templateResource     = $templateResource;
        $this->collectionFactory    = $collectionFactory;
        $this->collectionProcessor  = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(
        \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
    ): \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface {
        try {
            $this->templateResource->save($template);
            unset($this->instances[$template->getTemplateId()]);
        } catch (\Magento\Framework\Exception\ValidatorException $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Could not save the template: %1', $e->getMessage()),
                $e
            );
        }

        return $template;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $templateId): \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface
    {
        if (empty($this->instances[$templateId])) {
            /** @var CategoryTemplate $template */
            $template = $this->templateFactory->create();

            $this->templateResource->load($template, $templateId);

            if (!$template->getTemplateId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The template with the "%1" ID wasn\'t found. Verify the ID and try again.', $templateId)
                );
            }

            $this->instances[$templateId] = $template;
        }

        return $this->instances[$templateId];
    }

    /**
     * @inheritDoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria
    ): \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateSearchResultsInterface {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): bool
    {
        try {
            $this->templateResource->delete($template);
            unset($this->instances[$template->getTemplateId()]);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __('The "%1" template couldn\'t be removed.', $template->getTemplateId())
            );
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $templateId): bool
    {
        $this->delete($this->getById($templateId));

        return true;
    }
}
