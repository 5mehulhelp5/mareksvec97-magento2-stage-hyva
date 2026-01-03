<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigPayment;
use Hyva\Checkout\Model\MethodMetaData\IconRenderer;
use Hyva\Checkout\Model\MethodMetaData\SubtitleRenderer;
use Magento\Payment\Model\MethodInterface as PaymentMethodInterface;

/**
 * @deprecated has been replaced with a subject specific PaymentMethodMetaData
 * @see PaymentMethodMetaData
 */
class MethodMetaData extends AbstractMethodMetaData
{
    private SystemConfigPayment $systemConfigPayment;

    public function __construct(
        IconRenderer $iconRenderer,
        SubtitleRenderer $subtitleRenderer,
        PaymentMethodInterface $method,
        SystemConfigPayment $systemConfigPayment,
        array $data = []
    ) {
        parent::__construct($iconRenderer, $subtitleRenderer, $method, $data);

        $this->systemConfigPayment = $systemConfigPayment;
    }

    public function canRenderIcon(): bool
    {
        if (! $this->systemConfigPayment->canDisplayMethodIcons()) {
            return false;
        }

        $icon = $this->getData('icon');

        return parent::canRenderIcon() && (is_array($icon) || is_string($icon));
    }
}
