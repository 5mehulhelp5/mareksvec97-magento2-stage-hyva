<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Form\EntityField\EavEntityAddress\PostcodeAttributeField;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Directory\Helper\Data as DirectoryDataHelper;
use Magento\Quote\Api\Data\AddressInterface;

class WithPostcodeModifier implements EntityFormModifierInterface
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
            'applyZipcodeRequirementByCountryValue',
            'form:build',
            fn ($form) => $this->applyZipcodeRequirementByCountryValue($form)
        );

        return $form;
    }

    protected function applyZipcodeRequirementByCountryValue(EntityFormInterface $form): void
    {
        $countryField = $form->getField(AddressInterface::KEY_COUNTRY_ID);

        if ($countryField !== null && $countryField->getValue()) {
            $postcodeField = $form->getField(AddressInterface::KEY_POSTCODE);

            if ($postcodeField instanceof PostcodeAttributeField) {
                $postcodeField->setIsRequired(! $this->directoryDataHelper->isZipCodeOptional($countryField->getValue()));
            }
        }
    }
}
