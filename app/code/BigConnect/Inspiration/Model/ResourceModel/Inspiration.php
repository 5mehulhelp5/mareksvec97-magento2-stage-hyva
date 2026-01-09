<?php
namespace BigConnect\Inspiration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Inspiration extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('bigconnect_inspiration', 'entity_id');
    }
}
