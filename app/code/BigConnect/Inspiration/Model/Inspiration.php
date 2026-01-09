<?php
namespace BigConnect\Inspiration\Model;

use Magento\Framework\Model\AbstractModel;

class Inspiration extends AbstractModel
{
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DISABLED = 'disabled';

    protected function _construct(): void
    {
        $this->_init(\BigConnect\Inspiration\Model\ResourceModel\Inspiration::class);
    }
}
