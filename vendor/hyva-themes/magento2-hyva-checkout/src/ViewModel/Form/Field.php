<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Form;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Design\SystemConfigFormFields;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigDesign;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Field implements ArgumentInterface
{
    protected SystemConfigDesign $systemConfigDesign;

    public function __construct(
        SystemConfigDesign $systemConfigDesign
    ) {
        $this->systemConfigDesign = $systemConfigDesign;
    }

    public function designConfig(): SystemConfigFormFields
    {
        return $this->systemConfigDesign->formFields();
    }
}
