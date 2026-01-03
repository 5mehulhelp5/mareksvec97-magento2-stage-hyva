<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement;

class Submit extends Button
{
    protected string $type = 'submit';

    public function getId(): string
    {
        return $this->getData('id') ?? 'submit';
    }

    public function getLabel(): string
    {
        $label = parent::getLabel();

        return empty($label) ? 'Submit' : $label;
    }

    public function getLayoutAlias(): string
    {
        return $this->getData('layout_alias') ?? 'submit';
    }

    public function getMethod(): string
    {
        return $this->getData('method') ?? 'submit';
    }

    public function getAttributes(): array
    {
        $this->setClassAttributeValue('btn-primary');

        return parent::getAttributes();
    }
}
