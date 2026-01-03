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

class WithLabelElementModifier implements EntityFormModifierInterface
{
    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'applyLabelModifications',
            'form:build',
            function (EntityFormInterface $form) {
                foreach ($form->getFields() as $field) {
                    $this->applyLabelModifications($field);
                }
            }
        );

        return $form;
    }

    public function applyLabelModifications(EntityFieldInterface $field)
    {
        $id = $field->getId();
        $label = $field->getLabel();
        $classes = $field->getData(EntityFormElementInterface::CLASS_ELEMENT) ?? [];

        if ($id) {
            $classes['label']['field'] = 'label-' . $id;
        }

        if (empty($label)) {
            $classes['label']['screen_reader_only'] = 'sr-only';
            $field->setData(EntityFormElementInterface::LABEL, 'Form field');
        }

        $field->setData(EntityFormElementInterface::CLASS_ELEMENT, $classes);

        if ($field->hasRelatives()) {
            foreach ($field->getRelatives() as $relative) {
                if ($relative instanceof EntityFieldInterface) {
                    $this->applyLabelModifications($relative);
                }
            }
        }
    }
}
