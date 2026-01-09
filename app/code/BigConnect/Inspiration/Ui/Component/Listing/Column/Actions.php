<?php
declare(strict_types=1);

namespace BigConnect\Inspiration\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        private UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $actions = $this->getData('action_list') ?? [];

        foreach ($dataSource['data']['items'] as &$item) {
            $id = $item['entity_id'] ?? null;
            if (!$id) {
                continue;
            }

            foreach ($actions as $actionName => $action) {
                $path   = $action['path'] ?? '';
                $params = $action['params'] ?? [];

                // params mapping: entity_id => entity_id (z row)
                $resolvedParams = [];
                foreach ($params as $paramName => $fieldName) {
                    $resolvedParams[$paramName] = $item[$fieldName] ?? $id;
                }

                $item[$this->getData('name')][$actionName] = [
                    'href'   => $this->urlBuilder->getUrl($path, $resolvedParams),
                    'label'  => $action['label'] ?? ucfirst((string)$actionName),
                    'hidden' => false,
                ];

                if (!empty($action['confirm'])) {
                    $item[$this->getData('name')][$actionName]['confirm'] = $action['confirm'];
                }
            }
        }

        return $dataSource;
    }
}
