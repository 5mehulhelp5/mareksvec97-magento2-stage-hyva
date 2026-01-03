<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Ui\DataProvider\CategoryTemplate\Form\Modifier;

use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;

class General implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    const FIELDSET_TEMPLATE_INFORMATION = 'template_information';

    const FIELD_CATEGORY_IDS = 'category_ids';

    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate
     */
    protected $templateResource;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * General constructor.
     *
     * @param \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository,
        \Ecwhim\SeoTemplates\Model\ResourceModel\CategoryTemplate $templateResource,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->templateRepository = $templateRepository;
        $this->templateResource   = $templateResource;
        $this->dataPersistor      = $dataPersistor;
        $this->request            = $request;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $templateId   = $this->request->getParam(CategoryTemplateInterface::TEMPLATE_ID);
        $templateData = (array)$this->dataPersistor->get(
            \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface::ENTITY_TYPE_CATEGORY_TEMPLATE
        );
        $this->dataPersistor->clear(
            \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface::ENTITY_TYPE_CATEGORY_TEMPLATE
        );

        if ($templateId) {
            try {
                /** @var \Ecwhim\SeoTemplates\Model\CategoryTemplate $template */
                $template = $this->templateRepository->getById((int)$templateId);

                if (!$template->getApplyToAllCategories()) {
                    $categoryIds = $this->templateResource->getAssignedCategoryIds([$template->getTemplateId()]);

                    $template->setData(self::FIELD_CATEGORY_IDS, $categoryIds);
                }

                $templateData = array_replace_recursive($template->getData(), $templateData);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            }
        }

        $data[$templateId] = $templateData;

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeTypeField($meta);

        return $meta;
    }

    /**
     * @param array $meta
     * @return array
     */
    protected function customizeTypeField(array $meta): array
    {
        $templateId = (int)$this->request->getParam(CategoryTemplateInterface::TEMPLATE_ID);

        if ($templateId) {
            $meta = array_merge_recursive(
                $meta,
                [
                    self::FIELDSET_TEMPLATE_INFORMATION => [
                        'children' => [
                            CategoryTemplateInterface::TYPE => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }

        return $meta;
    }
}
