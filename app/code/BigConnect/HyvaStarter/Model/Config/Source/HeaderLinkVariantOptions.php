<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class HeaderLinkVariantOptions implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'default', 'label' => __('Default')],
            ['value' => 'cta', 'label' => __('CTA')],
        ];
    }
}
