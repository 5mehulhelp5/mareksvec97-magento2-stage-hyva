<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView;

use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Component\AddressTypeInterface;

/**
 * @method array getAddress()
 */
interface MagewireAddressFormInterface
{
    public const ADDRESS_PROPERTY = 'address';

    /**
     * Instantiate form.
     */
    public function getForm(): EntityFormInterface;

    /**
     * Instantiate form ready for the public.
     */
    public function getPublicForm(bool $build = false): EntityFormInterface;

    public function showFormModal(): void;

    public function hideFormModal(): void;

    /**
     * Submit the form.
     */
    public function submit(): bool;

    /**
     * Save interim
     */
    public function store(): bool;

    /**
     * Partially save address values.
     */
    public function save(bool $auto = false): bool;

    public function edit(): void;

    public function create(): void;

    public function canAutoSave(): bool;

    public function canCancel(): bool;

    /**
     * Populate public form address data property for Magewire to read.
     */
    public function populateAddressProperty(): array;

    public function getAddressType(): AddressTypeInterface;

    /**
     * @deprecated timeout is has become a default component property.
     */
    public function getAutoSaveTimeout(): int;

    /**
     * @deprecated determination of whether the form can be saved to the address book is provided via a form modifier.
     */
    public function canSaveToAddressBook(): bool;
}
