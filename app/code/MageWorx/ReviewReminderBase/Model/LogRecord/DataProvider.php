<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\LogRecord;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use MageWorx\ReviewReminderBase\Model\LogRecord;
use MageWorx\ReviewReminderBase\Model\ResourceModel\LogRecord\CollectionFactory;

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
        /** @var LogRecord $logRecord */
        foreach ($items as $logRecord) {
            $this->loadedData[$logRecord->getId()] = $logRecord->getData();
        }
        $data = $this->dataPersistor->get('mageworx_reviewreminderbase_logRecord');
        if (!empty($data)) {
            $logRecord = $this->collection->getNewEmptyItem();
            $logRecord->setData($data);
            $this->loadedData[$logRecord->getId()] = $logRecord->getData();
            $this->dataPersistor->clear('mageworx_reviewreminderbase_logRecord');
        }

        return $this->loadedData ?? [];
    }
}
