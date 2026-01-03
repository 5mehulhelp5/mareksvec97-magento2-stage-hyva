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

class Image extends AbstractEntityFormElement
{
    public function getLayoutAlias(): string
    {
        return parent::getLayoutAlias() ?? 'image';
    }

    public function render(): string
    {
        if (! $this->hasAttribute('src') && $this->hasData('src')) {
            $this->setAttribute('src', $this->getData('src'));
        }
        if (! $this->hasAttribute('alt') && $this->hasData('alt')) {
            $this->setAttribute('alt', $this->getData('alt'));
        }

        return parent::render();
    }
}
