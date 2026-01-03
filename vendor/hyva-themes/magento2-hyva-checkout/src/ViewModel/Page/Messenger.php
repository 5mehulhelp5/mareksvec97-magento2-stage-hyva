<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Page;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;

class Messenger implements ArgumentInterface
{
    public function renderErrorEvent(Template $block): string
    {
        $eventPrefix = $block->getData('event_prefix') ?? 'evaluation:event';
        return $eventPrefix . ':error';
    }

    public function renderSuccessEvent(Template $block): string
    {
        $eventPrefix = $block->getData('event_prefix') ?? 'evaluation:event';
        return $eventPrefix . ':success';
    }

    public function getDefaultMessageText(): string
    {
        return 'Something went wrong.';
    }
}
