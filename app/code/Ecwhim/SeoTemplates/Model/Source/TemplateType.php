<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Source;

class TemplateType implements \Magento\Framework\Data\OptionSourceInterface
{
    const META_TITLE       = 'meta_title';
    const META_KEYWORDS    = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        $types = [];

        foreach ($this->getTypes() as $key => $value) {
            $types[] = ['value' => $key, 'label' => $value];
        }

        return $types;
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return [
            self::META_TITLE       => __('Meta Title'),
            self::META_KEYWORDS    => __('Meta Keywords'),
            self::META_DESCRIPTION => __('Meta Description')
        ];
    }
}
