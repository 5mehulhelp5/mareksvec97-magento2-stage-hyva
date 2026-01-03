<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement;

use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Element\Template;

/**
 * @deprecated Use the AbstractRenderer instead.
 * @see \Hyva\Checkout\Model\Form\EntityFormElement\Renderer\AbstractRenderer
 */
interface RendererInterface
{
    /**
     * Render form element HTML.
     */
    public function render(EntityFormElementInterface $element): string;

    /**
     * @deprecated use method renderWithTemplate instead.
     */
    public function renderAs(string $alias, EntityFormElementInterface $element): string;

    /**
     * Render form element label HTML.
     */
    public function renderLabel(EntityFormElementInterface $element): string;

    /**
     * Render form element tooltip HTML.
     */
    public function renderTooltip(EntityFormElementInterface $element): string;

    /**
     * Attempts to render the content within the block or container associated with the "before" alias.
     */
    public function renderBefore(EntityFormElementInterface $element): string;

    /**
     * Attempts to render the content within the block or container associated with the "after" alias.
     */
    public function renderAfter(EntityFormElementInterface $element): string;

    /**
     * @return Template|false
     * @throws NotFoundException
     */
    public function resolveBlock(EntityFormElementInterface $element, ?callable $filter = null);
}
