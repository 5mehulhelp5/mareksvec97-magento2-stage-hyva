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

class WithBaseMagewireAttributesModifier implements EntityFormModifierInterface
{
    private string $dataPropertyName;

    public function __construct(
        string $dataPropertyName = 'data'
    ) {
        $this->dataPropertyName = $dataPropertyName;
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $applyBaseMagewireAttributes = function ($fields) use (&$applyBaseMagewireAttributes) {
            foreach ($fields as $field) {
                if (! $field->hasAttributesStartingWith('wire:model')) {
                    $field->setAttribute('wire:model.defer', $field->getTracePath($this->dataPropertyName));
                }
                if ($field->hasRelatives()) {
                    $applyBaseMagewireAttributes($field->getRelatives());
                }
            }
        };

        return $form->registerModificationListener(
            'applyBaseMagewireAttributes',
            'form:boot:magewire',
            fn () => $applyBaseMagewireAttributes($form->getFields())
        );
    }
}
