<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Unsubscribed;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Unsubscribed\CollectionFactory;
use MageWorx\ReviewReminderBase\Model\Unsubscribed;

class DataProvider extends AbstractDataProvider
{
    /**
     * Loaded data cache
     *
     * @var array
     */
    protected $loadedData;

    /**
     * Data persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Unsubscribed $unsubscribed */
        foreach ($items as $unsubscribed) {
            $this->loadedData[$unsubscribed->getId()] = $unsubscribed->getData();

        }
        $data = $this->dataPersistor->get('mageworx_reviewreminderbase_unsubscribed');
        if (!empty($data)) {
            $unsubscribed = $this->collection->getNewEmptyItem();
            $unsubscribed->setData($data);
            $this->loadedData[$unsubscribed->getId()] = $unsubscribed->getData();
            $this->dataPersistor->clear('mageworx_reviewreminderbase_unsubscribed');
        }

        return $this->loadedData ?? [];
    }
}
