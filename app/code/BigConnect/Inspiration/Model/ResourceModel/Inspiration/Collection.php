<?php
namespace BigConnect\Inspiration\Model\ResourceModel\Inspiration;

use BigConnect\Inspiration\Model\Inspiration as InspirationModel;
use BigConnect\Inspiration\Model\ResourceModel\Inspiration as InspirationResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(InspirationModel::class, InspirationResource::class);
    }
}
