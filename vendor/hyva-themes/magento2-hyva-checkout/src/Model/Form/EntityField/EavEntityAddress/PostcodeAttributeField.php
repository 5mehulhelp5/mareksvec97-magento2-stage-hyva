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
use Hyva\Checkout\Model\Form\EntityFieldInterface;

class PostcodeAttributeField extends EavAttributeField
{
    private bool $required = true;

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setIsRequired(bool $value): self
    {
        $this->required = $value;
        return $this;
    }
}
