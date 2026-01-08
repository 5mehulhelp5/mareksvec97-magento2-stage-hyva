<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class HeaderLinkIconOptions implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'sparkles', 'label' => __('Sparkles')],
            ['value' => 'factory', 'label' => __('Factory')],
            ['value' => 'info', 'label' => __('Info')],
            ['value' => 'phone', 'label' => __('Phone')],
            ['value' => 'mail', 'label' => __('Mail')],
            ['value' => 'map', 'label' => __('Map')],
            ['value' => 'message', 'label' => __('Message')],
            ['value' => 'star', 'label' => __('Star')],
        ];
    }
}
