<?php
namespace BigConnect\Inspiration\Model\Source;

use BigConnect\Inspiration\Model\Inspiration;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => Inspiration::STATUS_APPROVED, 'label' => __('Approved')],
            ['value' => Inspiration::STATUS_DISABLED, 'label' => __('Disabled')],
        ];
    }
}
