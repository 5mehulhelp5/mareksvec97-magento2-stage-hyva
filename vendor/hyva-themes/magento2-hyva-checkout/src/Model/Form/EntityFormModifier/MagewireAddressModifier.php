<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Magewire\Checkout\AddressView\AbstractMagewireAddressForm;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigFixesWorkarounds;
use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Hyva\Checkout\Magewire\Checkout\AddressView\MagewireAddressFormInterface as FormInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Serialize\SerializerInterface;
use Magewirephp\Magewire\Component;
use RuntimeException;

class MagewireAddressModifier implements EntityFormModifierInterface
{
    protected SerializerInterface $serializer;
    protected Escaper $escaper;

    private SystemConfigFixesWorkarounds $systemConfigFixesWorkarounds;

    public function __construct(
        SerializerInterface $serializer,
        Escaper $escaper,
        ?SystemConfigFixesWorkarounds $systemConfigFixesWorkarounds = null
    ) {
        $this->serializer = $serializer;
        $this->escaper = $escaper;
        $this->systemConfigFixesWorkarounds = $systemConfigFixesWorkarounds
            ?: ObjectManager::getInstance()->get(SystemConfigFixesWorkarounds::class);
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'applyMagewireRequirements',
            'form:build:magewire',
            fn (EntityFormInterface $form, AbstractMagewireAddressForm $component) => $this->applyMagewireRequirements($component, $form)
        );

        return $form;
    }

    public function applyMagewireRequirements(AbstractMagewireAddressForm $component, EntityFormInterface $form): EntityFormInterface
    {
        $this->applyHyvaValidationApiModifications($component, $form);
        $this->applyWireModelModifications($component, $form);
        $this->applyCountryModifications($component, $form);

        return $form;
    }

    /**
     * @todo requires more work on the wire.defer attribute, to support grouped fields. This is
     *       currently purely focused on relatives having the same ID as their ancestor. When this is
     *       not the case, it should change the path from a numeric value into the actual field id.
     *
     *       Example:
     *           form -> ancestor[a] -> relative [b] = wire:model.defer='address.a.b'
     *           form -> ancestor[a] -> ancestor/relative [b] -> relative[c] = wire:model.defer='address.a.b.c'
     *
     *       In other words, it requires much better recursive support.
     */
    public function applyWireModelModifications(
        Component $component,
        EntityFormInterface $form,
        ?EntityFieldInterface $ancestor = null,
        ?EntityFieldInterface $root = null
    ): EntityFormInterface {
        if ($ancestor === null && $root === null) {
            if (! array_key_exists(FormInterface::ADDRESS_PROPERTY, $component->getPublicProperties())) {
                throw new RuntimeException(
                    sprintf('Public property %s is required for address binding purposes.', FormInterface::ADDRESS_PROPERTY)
                );
            }
        }

        // Grep the relatives from its ancestor if available.
        $fields = $ancestor ? $ancestor->getRelatives() : $form->getFields();

        foreach ($fields as $field) {
            $addressProperty = FormInterface::ADDRESS_PROPERTY . '.' . $field->getId();

            $field->setAttribute('wire:loading.class', 'loading');
            $field->setAttribute('wire:loading.attr', 'disabled');
            $field->setAttribute('wire:target', $root ? $root->getId() : $field->getId());

            if ($field->hasRelatives()) {
                $this->applyWireModelModifications($component, $form, $field, $root ?? $field);
            }
            if ($field->hasNamesakeAncestor() || $field->hasNamesakeRelatives()) {
                $addressProperty .= '.' . $field->getPosition();
            }
            if ($component->hasErrors()) {
                $field->setData(EntityFormElementInterface::CLASS_WRAPPER, $field->getWrapperClass() + ['field-error']);
            }

            $field->setAttribute('wire:auto-save', $form->getNamespace());
            $field->setAttribute('wire:model.defer', $addressProperty);

            if ($field->isAutoSave() && $this->systemConfigFixesWorkarounds->disableFormAutoSaving()) {
                $field->replaceAttribute('wire:auto-save', 'wire:auto-save.self');

                if ($component instanceof AbstractMagewireAddressForm) {
                    $component->setMagewireProperty('loader', 'address.' . $field->getId(), false);
                }
            }

            // Assign a unique wire:key for the field wrapper (if it exists and is being rendered).
            $field->setAttributeForSection('wrapper', 'wire:key', 'field-wrapper-' . $form->getNamespace() . '-' . str_replace('.', '-', $addressProperty));
        }

        return $form;
    }

    public function applyHyvaValidationApiModifications(AbstractMagewireAddressForm $component, EntityFormInterface $form): EntityFormInterface
    {
        foreach ($form->getFields() as $field) {
            if ($field instanceof EavAttributeField) {
                $field->setAttribute('data-attribute-address', 'true');
            }

            if ($field->getId()) {
                $hasValidationErrors = $component->hasError($field->getId());
                $field->setAttribute('data-magewire-is-valid', $hasValidationErrors ? '0' : '1');

                if ($hasValidationErrors) {
                    $field->setAttribute('data-msg-magewire', (string) $component->getError($field->getId()));
                }
            }

            $field->setValidationRule('magewire');
        }

        return $form;
    }

    public function applyCountryModifications(
        AbstractMagewireAddressForm $component,
        EntityFormInterface $form
    ): EntityFormInterface {
        $countryField = $form->getField(AddressInterface::KEY_COUNTRY_ID);

        /**
         * Decorates the 'country_id' field with a 'wire:select' directive to transform an element into a regular model.
         *
         * When applied with a 'wire:model' that includes the ".defer" modifier, this directive ensures
         * immediate data synchronization whenever a change is detected. However, it introduces a delay
         * in synchronization while the user interacts with the element, allowing them to make multiple
         * selections without triggering immediate updates.
         *
         * @see Magewirephp_Magewire::view/frontend/templates/page/js/magewire/directive/select.phtml
         * @doc https://github.com/magewirephp/magewire/blob/main/docs/Features.md#wire-select
         */
        if ($countryField
            && $countryField->getFrontendInput() === 'select'
            && $countryField->hasAttributesStartingWith('wire:model.defer')) {
            $countryField->setAttribute('wire:select.debounce.blur');
        }

        return $form;
    }
}
