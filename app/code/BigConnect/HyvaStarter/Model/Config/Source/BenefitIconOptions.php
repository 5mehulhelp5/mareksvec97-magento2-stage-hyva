<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BenefitIconOptions implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'shield', 'label' => __('Shield check')],
            ['value' => 'globe', 'label' => __('Globe')],
            ['value' => 'badge', 'label' => __('Badge check')],
            ['value' => 'thumb', 'label' => __('Thumb up')],
            ['value' => 'phone', 'label' => __('Phone')],
        ];
    }
}
