<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement;

class Url extends Clickable
{
    public function getLayoutAlias(): string
    {
        return 'url';
    }

    public function render(): string
    {
        $href = null;
        $target = $this->getData('target');

        if ($this->hasData('url')) {
            $href = $this->getData('url');
        } elseif ($this->hasData('value')) {
            $href = $this->getData('value');
        }

        if (is_string($href)) {
            $this->setAttribute('href', $href);
        }
        if (is_string($target)) {
            $this->setAttribute('target', $target);
        }

        return parent::render();
    }
}
