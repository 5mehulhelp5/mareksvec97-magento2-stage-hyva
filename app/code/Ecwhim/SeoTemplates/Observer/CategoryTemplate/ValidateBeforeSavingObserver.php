<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Observer\CategoryTemplate;

class ValidateBeforeSavingObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\Source\Scope
     */
    protected $scope;

    /**
     * @var \Magento\Store\Model\ResourceModel\Store
     */
    protected $storeResource;

    /**
     * @var \Ecwhim\SeoTemplates\Model\Source\CategoryTemplate\Type
     */
    protected $categoryTemplateType;

    /**
     * ValidateBeforeSavingObserver constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\Source\Scope $scope
     * @param \Magento\Store\Model\ResourceModel\Store $storeResource
     * @param \Ecwhim\SeoTemplates\Model\Source\CategoryTemplate\Type $categoryTemplateType
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\Source\Scope $scope,
        \Magento\Store\Model\ResourceModel\Store $storeResource,
        \Ecwhim\SeoTemplates\Model\Source\CategoryTemplate\Type $categoryTemplateType
    ) {
        $this->scope                = $scope;
        $this->storeResource        = $storeResource;
        $this->categoryTemplateType = $categoryTemplateType;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template */
        $template = $observer->getEvent()->getEntity();

        if (!$template->getName()) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Name is missing.')
            );
        }

        $this->validateScope($template);
        $this->validateStoreIds($template);
        $this->validateType($template);

        if (!$template->getContent()) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Content is missing.')
            );
        }
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateScope(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): bool
    {
        $scope = $template->getScope();

        if (!$scope) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Scope is missing.')
            );
        }

        if (!in_array($scope, array_keys($this->scope->getValues()))) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Scope is invalid.')
            );
        }

        return true;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateStoreIds(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): bool
    {
        if ($template->getScope() === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_STORE) {
            $storeIds = $template->getStoreIds();

            if (empty($storeIds)) {
                throw new \Magento\Framework\Exception\ValidatorException(
                    __('The Store IDs is missing.')
                );
            }

            if (!in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeIds)
            ) {
                $existingStoreIds = $this->getExistingStoreIds($storeIds);

                if (count($storeIds) !== count($existingStoreIds)) {
                    foreach ($storeIds as $storeId) {
                        if (!in_array($storeId, $existingStoreIds)) {
                            throw new \Magento\Framework\Exception\ValidatorException(
                                __(
                                    'The Store View with the "%1" ID wasn\'t found. Verify the ID and try again.',
                                    $storeId
                                )
                            );
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateType(\Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface $template): bool
    {
        $type = $template->getType();

        if (!$type) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Type is missing.')
            );
        }

        if (!in_array($type, array_keys($this->categoryTemplateType->getTypes()))) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Type is invalid.')
            );
        }

        return true;
    }

    /**
     * @param array $storeIds
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getExistingStoreIds(array $storeIds): array
    {
        $select = $this->storeResource->getConnection()->select();
        $select
            ->from($this->storeResource->getMainTable(), $this->storeResource->getIdFieldName())
            ->where($this->storeResource->getIdFieldName() . ' IN(?)', $storeIds);

        return $this->storeResource->getConnection()->fetchCol($select);
    }
}
