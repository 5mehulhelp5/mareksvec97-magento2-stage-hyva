<?php
namespace BigConnect\Inspiration\Ui\DataProvider;

use BigConnect\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class ListingDataProvider extends AbstractDataProvider
{
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
