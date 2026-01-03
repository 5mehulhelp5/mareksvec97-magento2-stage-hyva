<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement;

class Reset extends Button
{
    protected string $type = 'reset';

    public function getId(): string
    {
        return $this->getData('id') ?? 'reset';
    }

    public function getLabel(): string
    {
        $label = parent::getLabel();

        return empty($label) ? 'Reset' : $label;
    }
}
