<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Directory\Helper\Data as DirectoryDataHelper;
use Magento\Quote\Api\Data\AddressInterface;

class WithCountryModifier implements EntityFormModifierInterface
{
    protected DirectoryDataHelper $directoryDataHelper;

    public function __construct(
        DirectoryDataHelper $directoryDataHelper
    ) {
        $this->directoryDataHelper = $directoryDataHelper;
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'applyDefaultCountryId',
            'form:action:create',
            [$this, 'applyDefaultCountryId']
        );

        return $form;
    }

    public function applyDefaultCountryId(EntityFormInterface $form): EntityFormInterface
    {
        if ($form->hasField(AddressInterface::KEY_COUNTRY_ID)) {
            $form->getField(AddressInterface::KEY_COUNTRY_ID)->setValue($this->directoryDataHelper->getDefaultCountry());
        }

        return $form;
    }
}
