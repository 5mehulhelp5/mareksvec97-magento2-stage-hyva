<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

interface EntityFieldInterface extends EntityFormElementInterface
{
    public const AUTO_COMPLETE = 'auto_complete';
    public const IS_REQUIRED = 'is_required';
    public const DEFAULT_VALUE = 'default_value';
    public const IS_AUTO_SAVE = 'is_auto_save';
    public const VALUE = 'value';
    public const PREVIOUS_VALUE = 'previous_value';
    public const OPTIONS = 'options';
    public const INPUT_ALIAS = 'input_alias';
    public const INPUT = 'input';

    public function setValue($value, bool $canUseDefault = true): self;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * Empty field value.
     */
    public function empty(): self;

    /**
     * Reset field value back to its origin.
     */
    public function reset(): self;

    /**
     * Get the frontend type HTML for input element.
     */
    public function getFrontendInput(): string;

    /**
     * Get the frontend type alias HTML for input element.
     *
     * Example: Frontend input alias is being used for searching the right block renderer alias
     *          for a particular form field. So instead of the renderer looking for a block alias
     *          named "text" or "select", it can also look for "text_street" or "street_select".
     *
     * @deprecated this method has been deprecated in favor of getLayoutAlias() to eliminate redundant logic.
     *             the getLayoutAlias method, introduced in the abstract element object, extends alias functionality
     *             beyond entity fields, providing a more versatile solution.
     */
    public function getFrontendInputAlias(): ?string;

    /**
     * Whether the field is required.
     */
    public function isRequired(): bool;

    /**
     * Get entity autocomplete attribute value.
     */
    public function getAutocomplete(): string;

    /**
     * Validate if entity can show an autocomplete attribute and value.
     */
    public function hasAutocomplete(): bool;

    public function getDefaultValue();

    public function getConfig(): EntityFieldConfigInterface;

    /**
     * Check if it can automatically save itself if needed.
     */
    public function isAutoSave(): bool;

    /**
     * @return array<string, string>
     */
    public function getValidationRules(): array;

    public function setValidationRule(string $validationName, $value = true): self;

    public function removeValidationRule(string $validationName): self;
}
