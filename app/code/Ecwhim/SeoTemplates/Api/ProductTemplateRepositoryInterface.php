<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api;

/**
 * @api
 */
interface ProductTemplateRepositoryInterface
{
    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template
     * @return \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(
        \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template
    ): \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

    /**
     * @param int $templateId
     * @return \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $templateId): \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Ecwhim\SeoTemplates\Api\Data\ProductTemplateSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ): \Ecwhim\SeoTemplates\Api\Data\ProductTemplateSearchResultsInterface;

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface $template): bool;

    /**
     * @param int $templateId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $templateId): bool;
}
