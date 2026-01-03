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

class WithBaseIconsModifier extends AbstractEntityFormModifier
{
    protected array $iconsMapping = [];

    public function __construct(
        array $iconsMapping = [
            'email' => 'heroicons/outline/mail',
            'password' => 'heroicons/outline/key',
        ]
    ) {
        $this->iconsMapping = array_filter($iconsMapping);
    }

    /**
     * @throws FormException
     */
    public function apply(AbstractEntityForm $form): AbstractEntityForm
    {
        $form->registerModificationListener(
            'applyIcons',
            'form:build',
            function (AbstractEntityForm $form) {
                $form->modifyFields(function (AbstractEntityField $field) {
                    $icon = $field->getData('icon');

                    if ($icon === null && $icon = $this->searchIcon($field)) {
                        $field->setData('icon', $icon);
                    }
                });
            }
        );

        return $form;
    }

    protected function searchIcon(AbstractEntityField $field): ?string
    {
        return $this->iconsMapping[$field->getFrontendInput()] ?? null;
    }
}
