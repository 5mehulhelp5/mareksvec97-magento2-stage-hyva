<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Form\Field;

use Hyva\Checkout\Model\Config\Source\StreetRendererType;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigAddressForms as SystemConfigDeveloperAddressForms;

class Street implements ArgumentInterface
{
    protected SystemConfigDeveloperAddressForms $systemConfigDeveloperAddressForms;

    public function __construct(
        SystemConfigDeveloperAddressForms $systemConfigDeveloperAddressForms
    ) {
        $this->systemConfigDeveloperAddressForms = $systemConfigDeveloperAddressForms;
    }

    public function showAsTwoColumnGrid(): bool
    {
        return $this->systemConfigDeveloperAddressForms->getRendererType() === StreetRendererType::OPTION_TWO_COLUMN_GRID;
    }

    public function showAsOneColumnRow(): bool
    {
        return $this->systemConfigDeveloperAddressForms->getRendererType() === StreetRendererType::OPTION_ONE_COLUMN_ROW;
    }
}
