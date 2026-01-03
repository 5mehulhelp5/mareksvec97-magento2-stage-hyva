<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFormElement\Renderer\Element as ElementRenderer;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;

class EntityFieldRenderer extends ElementRenderer
{
    public const LAYOUT_ELEMENT_RENDERERS = 'entity-form.field-renderers';
    public const LAYOUT_ELEMENT_TOOLTIP = 'form-field.tooltip';
    public const LAYOUT_ELEMENT_LABEL = 'form-field.label';

    public function renderAs(string $alias, EntityFormElementInterface $element): string
    {
        $element = clone $element;
        $element->setData(EntityFieldInterface::INPUT_ALIAS, $alias);

        return $this->render($element);
    }

    public function renderTooltip(EntityFormElementInterface $element): string
    {
        if (! $element->hasTooltip()) {
            return '';
        }

        return $this->renderAccessory($element, 'tooltip', self::LAYOUT_ELEMENT_TOOLTIP);
    }

    /**
     * Returns the correct render type searching by specific to generic.
     *
     * Return types including a fallback:
     *   1. {form namespace}.{field id}.{field type}
     *   2. {form namespace}.{field id}.
     *   3. {field id}.{field type}
     *   4. {field id}
     *   5. {form namespace}.{field type}
     *   6. {type}
     *   7. text
     *
     * @var EntityFieldInterface $element
     * @var ?string $alias this argument is deprecated and will be removed in future versions.
     *                     please use $element->getLayoutAlias() instead.
     * @return array<int, string>
     */
    public function getRenderTypes(EntityFormElementInterface $element, ?string $alias = null): array
    {
        $id = $element->getId();
        $alias ??= $element->getLayoutAlias();
        $form = $element->getForm()->getNamespace();
        $types = [];

        if ($alias) {
            $types[] = $form . '.' . $id . '.' . $alias;
        }

        $types[] = $form . '.' . $id;

        if ($alias) {
            $types[] = $id . '.' . $alias;
        }

        $types[] = $id;

        if ($alias) {
            $types[] = $form . '.' . $alias;
            $types[] = $alias;
        }

        if ($element instanceof EntityFieldInterface) {
            $types[] = $element->getFrontendInput();
        }

        // Always rely on text as fallback.
        $types[] = 'text';

        return array_unique($types);
    }
}
