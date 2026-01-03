<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigAddressForm;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Quote\Api\Data\AddressInterface;

class WithTelephoneModifier implements EntityFormModifierInterface
{
    protected SystemConfigAddressForm $systemConfigShippingAddressForm;

    public function __construct(
        SystemConfigAddressForm $systemConfigShippingAddressForm
    ) {
        $this->systemConfigShippingAddressForm = $systemConfigShippingAddressForm;
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        /*
         * Form Field Modification :: Telephone
         *
         * System administrators have the flexibility to add custom tooltips for each form field,
         * both for shipping and billing addresses. By default, the Luma checkout includes tooltips
         * for telephone and email fields. These tooltips may be essential for merchants transitioning
         * from Luma to the Hyva Checkout.
         *
         * This modifier provides system administrators with the option to apply the Luma Checkout's
         * telephone tooltip when no custom tooltip description is set via the form field mapping.
         */
        $form->registerModificationListener(
            'applyTelephoneTooltip',
            'form:populate',
            [$this, 'applyTelephoneTooltip']
        );

        $form->registerModificationListener(
            'applyTelephoneValidationElements',
            'form:build:magewire',
            fn (AbstractEntityForm $form) => $this->applyTelephoneValidation($form)
        );

        return $form;
    }

    public function applyTelephoneTooltip(EntityFormInterface $form): EntityFormInterface
    {
        $field = $form->getField(AddressInterface::KEY_TELEPHONE);

        if ($field === null) {
            return $form;
        }

        $tooltip = $field->getTooltip();
        $alternative = $this->systemConfigShippingAddressForm->useLumaShippingAddressTelephoneTooltip() ? 'For delivery questions.' : null;

        $field->setData(EntityFormElementInterface::TOOLTIP, empty($tooltip) ? $alternative : ucfirst($tooltip));
        return $form;
    }

    public function applyTelephoneValidation(AbstractEntityForm $form): AbstractEntityForm
    {
        $form->modifyField(AddressInterface::KEY_TELEPHONE, function (AbstractEntityField $field) use ($form) {
            $field->setValidationRule('telephone');
        });

        return $form;
    }
}
