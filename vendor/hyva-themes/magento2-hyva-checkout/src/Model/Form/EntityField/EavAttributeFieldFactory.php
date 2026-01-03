<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFormFieldFactory;
use Hyva\Checkout\Model\Form\EntityFormInterface;

class EavAttributeFieldFactory extends EntityFormFieldFactory
{
    public const ACCESSOR = 'eav_fields';

    public function resolveClassType(string $id, string $type, EntityFormInterface $form): string
    {
        $result = parent::resolveClassType($id, $type, $form);

        return $result === EntityFieldInterface::class ? EavAttributeField::class : $result;
    }
}
