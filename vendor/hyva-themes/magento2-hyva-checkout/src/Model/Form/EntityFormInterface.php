<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

use Hyva\Checkout\Exception\FormSubmitException;

/**
 * @deprecated implementing this interface is now unnecessary; instead, custom forms should simply extend from
 *             AbstractEntityForm for seamless integration and less backward incompatible breaking changes.
 *
 *             Some existing APIs still require this interface which in terms of backwards incompatible breaking changes
 *             we can not change. Therefore, those will keep requiring this interface. New functionality should instead
 *             use a requirement of AbstractEntityForm.
 * @see AbstractEntityForm
 */
interface EntityFormInterface
{
    /**
     * Initialize form.
     */
    public function init(): EntityFormInterface;

    /**
     * Populate form fields.
     */
    public function populate(): EntityFormInterface;

    /**
     * Build for publication.
     */
    public function build(): EntityFormInterface;

    /**
     * Fill form.
     *
     * @return AbstractEntityForm
     */
    public function fill(array $values, array $fields = []): EntityFormInterface;

    /**
     * Unset all field values.
     */
    public function clear(): EntityFormInterface;

    /**
     * Resets all field values in the form back to their original values.
     */
    public function reset(): EntityFormInterface;

    /**
     * Sort form fields based on their position.
     */
    public function sort(): EntityFormInterface;

    /**
     * Submit the form.
     *
     * @throws FormSubmitException
     */
    public function submit(): bool;

    /**
     * Assign one or multiple fields as relatives to a main field. The relatives
     * are specified as an array of EntityFieldInterface instances or field identifiers (strings).
     *
     * @param array<int, EntityFormElementInterface|string> $elements
     */
    public function group(array $elements, EntityFormElementInterface $parent): EntityFormInterface;

    /**
     * Returns an array containing all available form fields. Each field is represented by a key-value pair,
     * where the key is the field identifier and the value is an instance of the EntityFieldInterface.
     *
     * @return array<string, EntityFieldInterface>
     */
    public function getFields(): array;

    /**
     * Checks if the form has any field entities and returns
     * a boolean value indicating their presence.
     */
    public function hasFields(): bool;

    /**
     * Checks if a specific form field exists within
     * the form and returns it if found.
     */
    public function getField(string $name): ?EntityFieldInterface;

    /**
     * Add form field.
     */
    public function addField(EntityFieldInterface $field): EntityFormInterface;

    /**
     * Flags a particular field to be deleted. By invoking this function, you indicate
     * that the field should be removed or cleared from the form.
     */
    public function removeField(EntityFieldInterface $field): EntityFormInterface;

    /**
     * Validate if form field exists.
     */
    public function hasField(string $name): bool;

    /**
     * Set form fields.
     *
     * @param EntityFieldInterface[] $fields
     */
    public function setFields(array $fields): EntityFormInterface;

    /**
     * Checks if a specific form element exists within
     * the form and returns it if found.
     */
    public function getElement(string $name): ?EntityFormElementInterface;

    /**
     * Add form element.
     */
    public function addElement(EntityFormElementInterface $field): EntityFormInterface;

    /**
     * Flags a particular element to be deleted. By invoking this function, you indicate
     * that the field should be removed or cleared from the form.
     */
    public function removeElement(EntityFormElementInterface $field): EntityFormInterface;

    public function hasElement(string $name): bool;

    /**
     * Allows you to restore a previously removed field without going through the
     * process of recreating it. It provides a convenient way to bring back
     * a field that was temporarily removed from a form.
     */
    public function reinstateElement(EntityFormElementInterface $element): EntityFormInterface;

    /**
     * Retrieve an array containing mixed field values indexed by field ids.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    public function toJson(): string;

    /**
     * Checks whether all fields in the form are empty or have no user-provided values.
     */
    public function isEmpty(): bool;

    /**
     * Returns the form title.
     */
    public function getTitle(): string;

    /**
     * Returns a form field instance.
     */
    public function createField(string $name, string $type = 'text', array $arguments = [], ?string $withFactory = null): EntityFieldInterface;

    /**
     * Returns a form element instance.
     */
    public function createElement(string $name, array $arguments = []): EntityFormElementInterface;

    /**
     * Get form name.
     */
    public function getNamespace(): string;

    /**
     * Returns a physical factory object resolved by its name.
     *
     * @doc The purpose of this method is to provide support for multiple factories without
     *      the need to extend the object. In certain exceptional scenarios, you may have
     *      objects that need to be created dynamically and added to the form.
     *
     *      A custom factory needs to be injected via /etc/frontend/di.xml.
     */
    public function getFactoryFor(string $name): object;

    public function getSaveService(): EntityFormSaveServiceInterface;

    /**
     * Intercept form-related events to customize the form or its fields.
     */
    public function registerModificationListener(string $name, string $event, callable $modifier): EntityFormInterface;

    /**
     * Unregister a form modification intent by its name.
     */
    public function unregisterModificationListener(string $name): EntityFormInterface;

    /**
     * Enables the dispatching of events at various stages of the form lifecycle.
     * Allowing to listen for these events and apply modifications only when necessary.
     */
    public function dispatchModificationHook(string $event, array $args = []): EntityFormInterface;
}
