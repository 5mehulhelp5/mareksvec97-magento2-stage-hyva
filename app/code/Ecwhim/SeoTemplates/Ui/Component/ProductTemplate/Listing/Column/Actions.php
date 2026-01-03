<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Ui\Component\ProductTemplate\Listing\Column;

use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    const URL_PATH_APPLY  = 'ecwhim_seotemplates/productTemplate/apply';
    const URL_PATH_EDIT   = 'ecwhim_seotemplates/productTemplate/edit';
    const URL_PATH_DELETE = 'ecwhim_seotemplates/productTemplate/delete';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Actions constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
        $this->escaper    = $escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as &$item) {

                if (isset($item[ProductTemplateInterface::TEMPLATE_ID])) {
                    $name         = $this->getData('name');
                    $templateId   = (int)$item[ProductTemplateInterface::TEMPLATE_ID];
                    $templateName = $this->escaper->escapeHtml($item[ProductTemplateInterface::NAME]);

                    $item[$name]['apply']  = [
                        'href'    => $this->urlBuilder->getUrl(
                            static::URL_PATH_APPLY,
                            [ProductTemplateInterface::TEMPLATE_ID => $templateId]
                        ),
                        'label'   => __('Apply'),
                        'post'    => true,
                        'confirm' => [
                            'title'   => __('Apply "%1"', $templateName),
                            'message' => __('Are you sure you want to apply "%1"?', $templateName)
                        ]
                    ];
                    $item[$name]['edit']   = [
                        'href'  => $this->urlBuilder->getUrl(
                            static::URL_PATH_EDIT,
                            [ProductTemplateInterface::TEMPLATE_ID => $templateId]
                        ),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href'    => $this->urlBuilder->getUrl(
                            static::URL_PATH_DELETE,
                            [ProductTemplateInterface::TEMPLATE_ID => $templateId]
                        ),
                        'label'   => __('Delete'),
                        'post'    => true,
                        'confirm' => [
                            'title'   => __('Delete "%1"', $templateName),
                            'message' => __('Are you sure you want to delete "%1"?', $templateName)
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
