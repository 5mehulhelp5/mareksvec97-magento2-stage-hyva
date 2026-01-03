<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityForm\EavAttributeBillingAddressForm;
use Hyva\Checkout\Model\Form\EntityForm\EavAttributeShippingAddressForm;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;

class WithClassAttributeModifier implements EntityFormModifierInterface
{
    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        return $form->registerModificationListener('applyFieldsClassAttribute', 'form:build', [$this, 'applyFieldsClassAttribute']);
    }

    public function applyFieldsClassAttribute(EntityFormInterface $form): EntityFormInterface
    {
        if ($form instanceof AbstractEntityForm) {
            $form->modifyFields(function (AbstractEntityField $field) {
                $this->assignWrapperClassAttribute($field);
                $this->assignElementClassAttribute($field);
            });

            return $form;
        }

        // Maintained for backwards compatibility with legacy code.
        foreach ($form->getFields() as $field) {
            $this->assignWrapperClassAttribute($field);
            $this->assignElementClassAttribute($field);
        }

        return $form;
    }

    public function assignWrapperClassAttribute(EntityFieldInterface $field): WithClassAttributeModifier
    {
        $wrapperClasses = $field->getWrapperClass();

        $wrapperClasses['wrapper'] = 'field-wrapper';
        $wrapperClasses['type'] = 'field-type-' . $field->getFrontendInput();
        $wrapperClasses['validation'] = 'field field-reserved';

        if ($field->getFrontendInput() !== $field->getLayoutAlias()) {
            $wrapperClasses['alias'] = 'field-alias-' . $field->getLayoutAlias();
        }
        if ($field->getId()) {
            $wrapperClasses['field'] = 'field-' . $field->getId();
        }

        if ($field->getForm() instanceof AbstractEntityForm) {
            $field->getForm()->modifyField($field, function (AbstractEntityField $field) use ($wrapperClasses) {
                $existingClasses = $field->getData(EntityFormElementInterface::CLASS_WRAPPER);

                if (is_array($existingClasses)) {
                    $field->setData(EntityFormElementInterface::CLASS_WRAPPER, array_merge($wrapperClasses, $existingClasses));
                }

                $field->setData(EntityFormElementInterface::CLASS_WRAPPER, $wrapperClasses);
            });

            return $this;
        }

        return $this;
    }

    public function assignElementClassAttribute(EntityFieldInterface $field): WithClassAttributeModifier
    {
        if ($field instanceof AbstractEntityField) {
            $field->modify(fn (AbstractEntityField $field) => $this->applyAddressClasses($field));

            return $this;
        }

        $this->applyAddressClasses($field);
        return $this;
    }

    protected function applyAddressClasses(AbstractEntityField|EntityFieldInterface $field): AbstractEntityField|EntityFieldInterface
    {
        $elementClasses = $field->getData(EntityFormElementInterface::CLASS_ELEMENT) ?? [];
        $elementClasses['type'] = $field->getFrontendInput();

        // Include the attribute code as a class name.
        if ($field instanceof EavAttributeField) {
            $elementClasses['attribute_code'] = $field->getAttributeCode();
        }
        // Mark field as address attribute when it is the native shipping or billing form.
        if (in_array($field->getForm()->getNamespace(), [EavAttributeShippingAddressForm::FORM_NAMESPACE, EavAttributeBillingAddressForm::FORM_NAMESPACE])) {
            $elementClasses['address_attribute'] = 'address-attribute';
        }
        // Apply a "disabled" class for those fields who are disabled.
        if ($field->getState() === EntityFormElementInterface::STATE_DISABLED) {
            $elementClasses['disabled'] = 'disabled:opacity-60';
        }

        $existingClasses = $field->getData(EntityFormElementInterface::CLASS_ELEMENT);
        $existingClasses['field-wrapper-inner'][] = $field->isRequired() ? 'required' : '';

        if (is_array($existingClasses)) {
            $field->setData(EntityFormElementInterface::CLASS_ELEMENT, array_merge($elementClasses, $existingClasses));
        }

        return $field;
    }
}
