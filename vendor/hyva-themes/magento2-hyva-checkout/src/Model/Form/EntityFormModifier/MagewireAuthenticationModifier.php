<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Component\AddressTypeInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magewirephp\Magewire\Component;

/**
 * @deprecated extracted the email field into its own dedicated component, allowing it to be removed from any address form.
 * @see \Hyva\Checkout\Magewire\Checkout\GuestDetails
 */
class MagewireAuthenticationModifier implements EntityFormModifierInterface
{
    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        return $form;
    }

    public function applyAddressTypeEmailAddressValue(
        EntityFormInterface $form,
        Component $component,
        AddressTypeInterface $addressType
    ): EntityFormInterface {
        $email = $form->getField(AddressInterface::KEY_EMAIL);

        if ($email === null) {
            return $form;
        }

        // Because we are in the form build magewire event, the form was already populated and
        // filled with any belonging values. We only try to set the value when the value is,
        // null this is different from when the user emptied the field manually.
        if ($email->getValue() === null) {
            $email->setValue($addressType->getQuoteAddress()->getEmail());
        }

        return $form;
    }
}
