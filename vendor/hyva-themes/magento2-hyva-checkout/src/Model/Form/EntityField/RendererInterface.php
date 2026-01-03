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

/**
 * @deprecated The form field renderer now is form element specific, it uses the following interface instead:
 * @see \Hyva\Checkout\Model\Form\EntityFormElement\RendererInterface
 */
interface RendererInterface
{
    /**
     * Render form field HTML.
     *
     * @param EntityFieldInterface $field
     * @param string $form
     * @param EntityFieldInterface|null $parent
     * @return string
     */
    public function render(
        EntityFieldInterface $field,
        string $form,
        ?EntityFieldInterface $parent = null
    ): string;

    /**
     * Render form field tooltip HTML.
     *
     * @param EntityFieldInterface $field
     * @return string
     */
    public function renderTooltip(EntityFieldInterface $field): string;

    /**
     * Render before HTML.
     *
     * @param EntityFieldInterface $field
     * @param string $form
     * @return string
     */
    public function renderBefore(EntityFieldInterface $field, string $form): string;

    /**
     * Render after HTML.
     *
     * @param EntityFieldInterface $field
     * @param string $form
     * @return string
     */
    public function renderAfter(EntityFieldInterface $field, string $form): string;
}
