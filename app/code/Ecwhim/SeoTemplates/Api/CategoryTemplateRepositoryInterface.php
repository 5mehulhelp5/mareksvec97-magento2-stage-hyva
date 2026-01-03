<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api;

/**
 * @api
 */
interface CategoryTemplateRepositoryInterface
{
    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     * @return \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(
        \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
    ): \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

    /**
     * @param int $templateId
     * @return \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $templateId): \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ): \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateSearchResultsInterface;

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): bool;

    /**
     * @param int $templateId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $templateId): bool;
}
