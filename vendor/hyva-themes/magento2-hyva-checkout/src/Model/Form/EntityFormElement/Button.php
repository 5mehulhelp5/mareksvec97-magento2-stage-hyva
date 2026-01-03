<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement;

class Button extends Clickable
{
    protected string $type = 'button';

    public function getAttributes(): array
    {
        $this->setClassAttributeValue('btn');

        return array_merge(['type' => $this->type], parent::getAttributes());
    }
}
