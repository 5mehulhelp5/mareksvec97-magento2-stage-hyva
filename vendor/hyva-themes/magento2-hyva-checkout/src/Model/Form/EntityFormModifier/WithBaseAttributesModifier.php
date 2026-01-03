<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;

class WithBaseAttributesModifier implements EntityFormModifierInterface
{
    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'applyBaseAttributes',
            'form:build',
            [$this, 'applyBaseAttributes']
        );

        return $form;
    }

    public function applyBaseAttributes(EntityFormInterface $form): EntityFormInterface
    {
        $form->setAttribute('id', $form->getNamespace());

        foreach ($form->getFields() as $field) {
            $this->assignBaseAttributes($form, $field);
        }

        // Finally sort all fields based on their sort order after the build process was completed.
        return $form->sort();
    }

    /**
     * Sets all default base attributes onto each field in the form.
     */
    public function assignBaseAttributes(EntityFormInterface $form, EntityFieldInterface $field): WithBaseAttributesModifier
    {
        /** @var EntityFieldInterface[] $relatives */
        $relatives = $field->getRelatives();

        $field->setAttribute('type', $field->getFrontendInput());
        $field->setAttribute('data-form', $form->getNamespace());
        $field->setAttribute('data-attribute', $field->getId());
        $field->setAttribute('data-order', $field->getSortOrder());
        $field->setAttribute('data-level', $field->getLevel());

        if ($field->getState() === EntityFormElementInterface::STATE_DISABLED) {
            $field->setAttribute('disabled');
        }
        if ($field->isRequired()) {
            $field->setAttribute('required');
        }

        $namesakes = [];

        if ($field->hasRelatives()) {
            $namesakes = array_filter(array_keys($relatives), function ($key) use ($field) {
                return is_string($key) ? strpos($key, $field->getId()) === 0 : $key;
            });

            // Fields can always be grouped at any stage, so let's run over all relatives
            // recursively to make sure they also have their attributes assigned.
            foreach ($relatives as $relative) {
                $this->assignBaseAttributes($form, $relative);
            }
        }

        // Handle HTML element id="...".
        if ($field->getId()) {
            $id = $form->getNamespace() . '-' . $field->getId();

            if (($field->hasAncestor() && $field->getId() === $field->getAncestor()->getId()) || $field->hasNamesakeRelatives()) {
                $id .= '-' . $field->getPosition();
            }

            $field->setAttribute('id', $id);
        }

        // Handle HTML element name="...".
        if ($field->getName()) {
            if (count($namesakes) !== 0) {
                $name = $field->getName() . '[' . $field->getPosition() . ']';
            }
            if ($field->hasAncestor() && $field->getId() === $field->getAncestor()->getId()) {
                $name = $field->getAncestor()->getName() . '[' . $field->getPosition() . ']';
            }

            $field->setAttribute('name', $name ?? $field->getName());
        }

        return $this;
    }
}
