<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Ui\DataProvider\ProductTemplate\Form\Modifier;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class General implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    const FIELDSET_TEMPLATE_INFORMATION = 'template_information';

    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected $templateRepository;

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
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->templateRepository = $templateRepository;
        $this->dataPersistor      = $dataPersistor;
        $this->request            = $request;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        $templateId   = $this->request->getParam(ProductTemplateInterface::TEMPLATE_ID);
        $templateData = (array)$this->dataPersistor->get(
            \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface::ENTITY_TYPE_PRODUCT_TEMPLATE
        );
        $this->dataPersistor->clear(
            \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface::ENTITY_TYPE_PRODUCT_TEMPLATE
        );

        if ($templateId) {
            try {
                /** @var \Ecwhim\SeoTemplates\Model\ProductTemplate $template */
                $template     = $this->templateRepository->getById((int)$templateId);
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
        $templateId = (int)$this->request->getParam(ProductTemplateInterface::TEMPLATE_ID);

        if ($templateId) {
            $meta = array_merge_recursive(
                $meta,
                [
                    self::FIELDSET_TEMPLATE_INFORMATION => [
                        'children' => [
                            ProductTemplateInterface::TYPE => [
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
