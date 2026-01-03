<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Source;

class Scope implements \Magento\Framework\Data\OptionSourceInterface
{
    const SCOPE_STORE  = 'store';
    const SCOPE_GLOBAL = 'global';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->getValues() as $key => $value) {
            $options[] = ['value' => $key, 'label' => $value];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return [
            self::SCOPE_STORE  => __('Store View'),
            self::SCOPE_GLOBAL => __('Global')
        ];
    }
}
