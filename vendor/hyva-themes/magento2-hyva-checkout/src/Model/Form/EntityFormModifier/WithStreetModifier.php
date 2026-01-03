<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\AddressForm\SystemConfigStreet;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Quote\Api\Data\AddressInterface;

class WithStreetModifier implements EntityFormModifierInterface
{
    protected SystemConfigStreet $systemConfigStreet;

    public function __construct(
        SystemConfigStreet $systemConfigStreet
    ) {
        $this->systemConfigStreet = $systemConfigStreet;
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        /*
         * Form Field Modification :: Street (+ its relatives)
         *
         * The default street field is automatically labeled based on the corresponding attribute.
         * In the system configuration, we have introduced a new feature that allows users to customize
         * the field labels based on the number of lines required by Magento's core. This configuration
         * enables dynamic visibility of field labels for each individual field.
         *
         * This modifier spreads each label to it's belonging field.
         */
        $form->registerModificationListener(
            'applyStreetLabels',
            'form:build',
            function (AbstractEntityForm $form) {
                return $form->modifyField(AddressInterface::KEY_STREET, function (AbstractEntityField $street) {
                    if ($this->systemConfigStreet->hasLabelFor(0)) {
                        $street->setData(EntityFormElementInterface::LABEL, $this->systemConfigStreet->getLabelFor(0));
                    }

                    foreach (array_values($street->getRelatives()) as $key => $field) {
                        $field->setData(EntityFormElementInterface::LABEL, $this->systemConfigStreet->getLabelFor($key + 1) ?? '');
                    }
                }, false);
            }
        );

        /*
         * Form Field Modification :: Street
         *
         * The rendering of a street can be customized based on a specified layout alias.
         * If a layout alias is provided, the renderer will prioritize using the frontend
         * input alias associated with it. However, if the frontend input alias is not set,
         * it will fall back to the original frontend input.
         *
         * In this case, the default alias for street is 'text'. Therefore, during the
         * rendering process, it will first search for 'street' in any form and then fallback
         * to 'text' if no matching alias is found.
         */
        $form->registerModificationListener(
            'applyStreetRenderAlias',
            'form:build',
            function (AbstractEntityForm $form) {
                return $form->modifyField(AddressInterface::KEY_STREET, function (AbstractEntityField $street) {
                    if (! $street->hasData(EntityFieldInterface::INPUT_ALIAS)) {
                        $street->setData(EntityFieldInterface::INPUT_ALIAS, AddressInterface::KEY_STREET);
                    }
                });
            }
        );

        /*
         * Form Field Modification :: Street
         *
         * Enhances street address form fields with configuration-aware labels and validation.
         *
         * Street address fields can render in multiple configurations (single line, multi-line,
         * with/without additional fields) based on system settings. Generic validation messages
         * like "This field is required" become ambiguous when users can't clearly identify
         * which specific address line failed validation.
         *
         * This modification applies contextual labels to street address relatives/sub-fields
         * to provide clearer validation feedback and improve user experience.
         */
        $form->registerModificationListener(
            'applyStreetErrorMessages',
            'form:build',
            function (AbstractEntityForm $form) {
                return $form->modifyField(AddressInterface::KEY_STREET, function (AbstractEntityField $street) {
                    if ($street->hasAttribute('data-msg-required')) {
                        return;
                    }

                    if (strlen($street->getLabel()) !== 0) {
                        $message = __('"%1" is a required field.', $street->getLabel());
                    }

                    $message ??= match ($street->getPosition()) {
                        0 => __('Street Address line one is a required field.'),
                        1 => __('Street Address line two is a required field.'),
                        2 => __('Street Address line three is a required field.'),

                        default => null
                    };

                    if ($message) {
                        $street->setAttribute('data-msg-required', $message);
                    }
                });
            }
        );

        return $form;
    }
}
