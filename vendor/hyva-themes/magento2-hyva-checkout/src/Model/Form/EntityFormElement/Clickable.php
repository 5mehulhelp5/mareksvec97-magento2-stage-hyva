<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement;

use Hyva\Checkout\Model\Form\AbstractEntityFormElement;

abstract class Clickable extends AbstractEntityFormElement
{
    public function getMethod(): string
    {
        return $this->getData('method') ?? 'click';
    }

    public function getLayoutAlias(): string
    {
        return parent::getLayoutAlias() ?? 'clickable';
    }
}
