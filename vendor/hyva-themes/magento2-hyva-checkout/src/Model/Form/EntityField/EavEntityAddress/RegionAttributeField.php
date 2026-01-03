<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField\EavEntityAddress;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;

class RegionAttributeField extends EavAttributeField
{
    public function getValue()
    {
        $value = parent::getValue();

        // Transform the region name to an ID when options are available.
        if ($value !== null && $this->hasOptions()) {
            foreach ($this->getOptions() as $option) {
                if ($option['label'] === $value) {
                    return $option['value'];
                }
            }
        }

        return $value;
    }
}
