<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Ui\DataProvider\ProductTemplate;

class FormDataProvider extends \Ecwhim\SeoTemplates\Ui\DataProvider\AbstractFormDataProvider
{
    /**
     * FormDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface $pool
     * @param \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Ui\DataProvider\Modifier\PoolInterface $pool,
        \Ecwhim\SeoTemplates\Model\ResourceModel\ProductTemplate\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $pool, $meta, $data);

        $this->collection = $collectionFactory->create();
    }
}
