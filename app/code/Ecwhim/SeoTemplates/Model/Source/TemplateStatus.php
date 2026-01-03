<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Source;

class TemplateStatus implements \Magento\Framework\Data\OptionSourceInterface
{
    const INACTIVE = 0;
    const ACTIVE   = 1;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        $types = [];

        foreach ($this->getStatuses() as $key => $value) {
            $types[] = ['value' => $key, 'label' => $value];
        }

        return $types;
    }

    /**
     * @return array
     */
    public function getStatuses(): array
    {
        return [
            self::INACTIVE => __('Inactive'),
            self::ACTIVE   => __('Active')
        ];
    }
}
