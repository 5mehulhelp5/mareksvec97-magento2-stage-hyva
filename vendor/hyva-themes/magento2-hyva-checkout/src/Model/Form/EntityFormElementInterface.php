<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

use Hyva\Checkout\Model\Form\EntityFormElement\RendererInterface;

interface EntityFormElementInterface
{
    public const FORM = 'form';
    public const POSITION = 'position';
    public const ANCESTOR = 'ancestor';
    public const RELATIVES = 'relatives';
    public const ID = 'id';
    public const NAME = 'name';
    public const LABEL = 'label';
    public const LEVEL = 'level';
    public const STATE = 'state';
    public const CLASS_ELEMENT = 'class_element';
    public const CLASS_WRAPPER = 'class_wrapper';
    public const VISIBLE = 'visible';
    public const TOOLTIP = 'tooltip';
    public const COMMENT = 'comment';

    public const STATE_ENABLED  = 1;
    public const STATE_DISABLED = 0;

    public function getId(): ?string;

    public function getName(): string;

    /**
     * Render element.
     */
    public function render(): string;

    /**
     * Validate if the element can become visible.
     */
    public function canRender(): bool;

    /**
     * Resolve element renderer.
     */
    public function getRenderer(): RendererInterface;

    /**
     * Get entity label.
     */
    public function getLabel(): string;

    /**
     * Get class attribute value for element.
     *
     * @param array<int|string, string|int> $combineWith
     */
    public function getClass(array $combineWith = []): string;

    /**
     * Render class attribute value for element.
     *
     * @param array<int|string, string|int> $combineWith
     */
    public function renderClass(array $combineWith = [], ?string $section = null): string;

    /**
     * Render class attribute value for element wrapper.
     *
     * @param array<int|string, string|int> $combineWith
     */
    public function renderWrapperClass(array $combineWith = []): string;

    /**
     * Get class attribute value as array.
     *
     * @param array<int|string, string|int> $combineWith
     */
    public function getWrapperClass(array $combineWith = []): array;

    /**
     * @deprecated method has been replaced with getWrapperClass to maintain uniformity
     *             with other method names within the class.
     *
     * @param array<int|string, string|int> $combineWith
     */
    public function getWrapperClasses(array $combineWith = []): array;

    public function getSortOrder(): int;

    /**
     * Returns the position within the level.
     */
    public function getPosition(): int;

    public function hasTooltip(): bool;

    public function getTooltip(): string;

    public function getLevel(): int;

    public function assignAncestor(EntityFormElementInterface $ancestor): self;

    public function hasAncestor(): bool;

    public function hasNamesakeAncestor(): bool;

    /**
     * @see self::hasAncestor()
     */
    public function getAncestor(): ?self;

    public function removeAncestor(): self;

    public function assignRelative(EntityFormElementInterface $relative): self;

    public function hasRelatives(): bool;

    /**
     * @return EntityFormElementInterface[]
     */
    public function getRelatives(): array;

    public function hasNamesakeRelatives(): bool;

    /**
     * @return EntityFormElementInterface[]
     */
    public function getNamesakeRelatives(): array;

    public function removeRelative(EntityFormElementInterface $element): self;

    public function getForm(): EntityFormInterface;

    public function enable(): self;

    public function disable(): self;

    public function hide(): self;

    public function show(): self;

    public function isVisible(): bool;

    /**
     * Returns if the element is enabled or disabled.
     *
     * @see self::STATE_DISABLED, self::STATE_ENABLED
     */
    public function getState(): int;

    public function addData(array $array);

    public function setData($key, $value = null);

    public function unsetData($key = null);

    public function getData($key = null, $index = null);
}
