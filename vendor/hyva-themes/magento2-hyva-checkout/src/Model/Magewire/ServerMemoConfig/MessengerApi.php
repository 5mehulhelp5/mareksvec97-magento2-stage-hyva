<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\ServerMemoConfig;

class MessengerApi extends AbstractConfigSection
{
    public function getData(): array
    {
        return [
            // Query selector class for the details Hyva_Checkout::page/messenger.phtml
            'querySelectorClass' => 'component-messenger'
        ];
    }
}
