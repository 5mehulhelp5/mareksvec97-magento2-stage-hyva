<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Hyva\Checkout\Model\Form\EntityField\EavAttributeMappingConfigInterface;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;

class WithEavAttributeModifier implements EntityFormModifierInterface
{
    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener('applyEavModifications', 'form:build', [$this, 'applyEavModifications']);
        return $form;
    }

    public function applyEavModifications(EntityFormInterface $form): EntityFormInterface
    {
        foreach (array_filter($form->getFields(), fn ($field) => $field instanceof EavAttributeField) as $field) {
            $this->assignConfigurableFieldSpanClass($field)
                 ->assignAttributeCodeCssClass($field);
        }

        return $form;
    }

    public function assignConfigurableFieldSpanClass(EavAttributeField $field): WithEavAttributeModifier
    {
        $wrapperClass = $field->getWrapperClass();

        // Map tailwind classes indexed by length field option values.
        $lengthClass = [
            0 => 'md:col-span-3',
            1 => 'md:col-span-6',
            2 => 'md:col-span-9',
            3 => 'md:col-span-12'
        ];

        /** @var EavAttributeMappingConfigInterface $config */
        $config = $field->getConfig();

        $wrapperClass['length'] = $lengthClass[$config->getLength()] ?? 'w-2/4';

        $field->setData(EntityFormElementInterface::CLASS_WRAPPER, $wrapperClass);
        return $this;
    }

    public function assignAttributeCodeCssClass(EavAttributeField $field): WithEavAttributeModifier
    {
        $cssClass = $field->getData(EntityFormElementInterface::CLASS_ELEMENT) ?? [];
        $cssClass['attribute_code'] = $field->getAttribute()->getAttributeCode();

        $field->setData(EntityFormElementInterface::CLASS_ELEMENT, $cssClass);
        return $this;
    }
}
