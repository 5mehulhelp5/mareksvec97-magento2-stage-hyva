<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Exception\FormException;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\AbstractEntityFormModifier;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;

class WithAutoCompleteAttributesModifier extends AbstractEntityFormModifier
{
    protected array $autocompleteAttributeFieldsMapping;

    public function __construct(
        array $autocompleteAttributeFieldsMapping = []
    ) {
        $this->autocompleteAttributeFieldsMapping = $autocompleteAttributeFieldsMapping;
    }

    /**
     * @throws FormException
     */
    public function apply(AbstractEntityForm $form): AbstractEntityForm
    {
        $form->registerModificationListener(
            'applyAutocompleteAttributes',
            'form:build',
            function (AbstractEntityForm $form) {
                $form->modifyFields(function (AbstractEntityField $field) {
                    $autocomplete = $field->getAutocomplete();

                    if (strlen($autocomplete) === 0) {
                        $autocomplete = $this->autocompleteAttributeFieldsMapping[$field->getTracePath()] ?? null;
                    }

                    if ($autocomplete) {
                        $field->setAttribute('autocomplete', $autocomplete);
                    }
                });
            }
        );

        return $form;
    }
}
