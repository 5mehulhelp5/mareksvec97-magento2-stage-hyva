<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigShippingBillingForm
{
    /**
     * @deprecated An autosave timeout is no longer required moving saving into a Evaluation API navigation validation task.
     * @see \Hyva\Checkout\Magewire\Component\AbstractForm::build()
     */
    public const XML_PATH_AUTOSAVE_TIMEOUT = 'hyva_themes_checkout/developer/shipping_billing_form/autosave_timeout';

    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @deprecated An autosave timeout is no longer required moving saving into a Evaluation API navigation validation task.
     * @see \Hyva\Checkout\Magewire\Component\AbstractForm::build()
     */
    public function getAutoSaveTimeout(): int
    {
        return 3000;
    }
}
