<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Component;

use Exception;
use Hyva\Checkout\Magewire\Checkout\AddressView\AbstractMagewireAddressForm;
use Hyva\Checkout\Model\Component\AddressTypeManagement;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityFormElement\Button;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Batch as EvaluationResultBatch;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Magento\Customer\Model\Address\Mapper as MapperAddress;
use Psr\Log\LoggerInterface;
use Rakit\Validation\Validator;

/**
 * @deprecated a replacement layer for AbstractMagewireAddressForm to keep backwards compatibility.
 *             new form should always be abstracted from the AbstractForm.
 *
 * @interal preparations for making shipping and billing forms compatible and backwards compatible
 *          with the existing forms based on the AbstractMagewireAddressForm.
 *
 * @see AbstractMagewireAddressForm
 * @see AbstractForm
 */
abstract class AbstractAddressForm extends AbstractForm
{
    protected $listeners = [
        'create', // triggers the create method on an emit 'create'.
        'edit'    // triggers the edit method on an emit 'edit'.
    ];

    private AddressTypeManagement $addressTypeManagement;
    private MapperAddress $mapperAddress;

    public function __construct(
        Validator $validator,
        AbstractEntityForm $form,
        LoggerInterface $logger,
        EvaluationResultBatch $evaluationResultBatch,
        AddressTypeManagement $addressTypeManagement,
        MapperAddress $mapperAddress
    ) {
        parent::__construct($validator, $form, $logger, $evaluationResultBatch);

        $this->addressTypeManagement = $addressTypeManagement;
        $this->mapperAddress = $mapperAddress;
    }

    public function construct(AbstractEntityForm $form): void
    {
        // Set default modal visibility.
        $this->misc['modal']['visible'] = false;

        try {
            $this->getForm()->registerModificationListener(
                '@internal_modifyToFieldsUsingModelDefer',
                'form:build:magewire',
                fn () => $this->modifyToFieldsUsingModelDefer()
            );
            $this->getForm()->registerModificationListener(
                '@internal_modifyCountryFieldToAutoSaveOnChange',
                'form:build:magewire',
                fn () => $this->modifyCountryFieldToAutoSaveOnChange()
            );
            $this->getForm()->registerModificationListener(
                '@internal_modifyToBackwardsCompatibleStylingRequirements',
                'form:build:magewire',
                fn () => $this->modifyToBackwardsCompatibleStylingRequirements()
            );
            $this->getForm()->registerModificationListener(
                '@internal_modifyToBackwardsCompatibleLoaderNotifications',
                'form:build:magewire',
                fn () => $this->modifyToBackwardsCompatibleLoaderNotifications()
            );
            $this->getForm()->registerModificationListener(
                '@internal_modifyWithDefaultMagewireAttributes',
                'form:build:magewire',
                fn () => $this->modifyWithDefaultMagewireAttributes()
            );
            $this->getForm()->registerModificationListener(
                '@internal_modifyToStreetFieldsUsingModelDefer',
                'form:build:magewire',
                fn () => $this->modifyToStreetFieldsUsingModelDefer()
            );
            $this->getForm()->registerModificationListener(
                '@internal_modifyToSupportCreatingCustomShippingAddress',
                'form:build:magewire',
                fn () => $this->modifyToIncludeModalSpecificFormElements()
            );
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }
    }

    public function showFormModal(): void
    {
        $this->misc['modal']['visible'] = true;

        $this->getEvaluationResultBatch()->misses(fn (EvaluationResult $result) => $result->hasAlias('address-modal'), function (EvaluationResultBatch $batch) {
            $batch->push(
                $batch->getFactory()
                    ->createEvent('address-form-modal-show')
                    ->withAlias('address-modal')
                    ->withDetails([
                        'type' => $this->getAddressType()->__toString()
                    ])
                    ->dispatch()
            );
        });
    }

    public function hideFormModal(): void
    {
        if (! $this->isModalVisible()) {
            return; // Ignore this method if the modal is already invisible.
        }

        $this->getEvaluationResultBatch()->misses(fn (EvaluationResult $result) => $result->hasAlias('address-modal'), function (EvaluationResultBatch $batch) {
            $batch->push(
                $batch->getFactory()
                    ->createEvent('address-form-modal-hide')
                    ->withAlias('address-modal')
                    ->withDetails([
                        'type' => $this->getAddressType()->__toString()
                    ])
                    ->dispatch()
            );
        });

        $this->misc['modal']['visible'] = false;
    }

    /**
     * Backwards compatible method for creating a new address.
     *
     * @see AbstractMagewireAddressForm::create()
     */
    public function create(): void
    {
        $this->autosave = false;

        $this->getForm()->reset();
        $addressType = $this->addressTypeManagement->getAddressTypeShipping();

        $this->showFormModal();

        $this->dispatchFormModificationHook('action:create');
        $this->dispatchFormModificationHook(sprintf('%s:action:create', $addressType));
    }

    /**
     * Backwards compatible method for editing a new address.
     *
     * @see AbstractMagewireAddressForm::edit()
     */
    public function edit(): void
    {
        $this->autosave = false;

        $addressType = $this->addressTypeManagement->getAddressTypeShipping();
        $address = $addressType->getQuoteAddress()->exportCustomerAddress();

        $this->showFormModal();

        $this->dispatchFormModificationHook('form:action:edit', [$address]);
        $this->dispatchFormModificationHook(sprintf('form:%s:action:edit', $addressType), [$address, $this]);
    }

    abstract protected function getAddressType();

    protected function isModalVisible(): bool
    {
        return $this->misc['modal']['visible'] === true;
    }

    /**
     * Getter method for the address type management.
     */
    protected function getAddressTypeManagement(): AddressTypeManagement
    {
        return $this->addressTypeManagement;
    }

    /**
     * Getter method for the address mapper.
     */
    protected function getAddressMapper(): MapperAddress
    {
        return $this->mapperAddress;
    }

    protected function dispatchFormModificationHook(string $hook, array $args = []): AbstractEntityForm
    {
        parent::dispatchFormModificationHook($hook, $args);

        $form = $this->getForm();

        // @deprecated we're in a Magewire-driven form, where only hooks affixed with ':magewire' should be used.
        $form->dispatchModificationHook(sprintf('form:%s', $hook), array_merge([$this], $args));
        // @deprecated avoid using namespaced modifiers since typed modifier objects are already injected.
        $form->dispatchModificationHook(sprintf('form:%s:%s', $form->getNamespace(), $hook), array_merge([$this], $args));

        return $form;
    }

    /**
     * Form Modifications: Assigns a default `wire:model` attribute with the `defer` modifier to all fields that do
     * not already have an attribute starting with `wire:model. This ensures that fields without
     * specific `wire:model` attributes can still be individually customized as needed.
     */
    private function modifyToFieldsUsingModelDefer(): void
    {
        $this->getForm()->modifyFields(function (AbstractEntityField $field) {
            if (! $field->hasAttributesStartingWith('wire:model')) {
                $field->setAttribute('wire:model.defer', $field->getTracePath('data'));
            }

            $field->setAttribute('wire:auto-save', $this->getForm()->getNamespace());
        });
    }

    /**
     * Form Modifications: The country field must stay up-to-date with the quote, requiring immediate
     * synchronization with the backend after selection. This is achieved by applying a specific
     * <select> directive with `debounce` and `blur` modifiers to ensure proper syncing behavior.
     *
     * @see Magewirephp_Magewire::page/js/magewire/directive/select.phtml
     */
    private function modifyCountryFieldToAutoSaveOnChange(): void
    {
        $this->getForm()->modifyField('country_id', function (AbstractEntityField $field) {
            if ($field->getFrontendInput() === 'select' && $field->hasAttributesStartingWith('wire:model.defer')) {
                $field->setAttribute('wire:select.debounce.blur');
            }
        });
    }

    /**
     * Form Modifications: The form previously used the older AbstractMagewireAddressForm abstraction along with a
     * rendering template that could only render fields, not both fields and elements. This approach relied on
     * hard-coded form classes for styling. The implementation has now been made backward compatible.
     *
     * @see AbstractMagewireAddressForm (deprecated)
     * @see Hyva_Checkout::checkout/address-view/address-form.phtml (deprecated)
     */
    private function modifyToBackwardsCompatibleStylingRequirements()
    {
        // Backwards compatibility: include the old "space-y-2" and "address-form" classes.
        $this->getForm()->modify(function (AbstractEntityForm $form) {
            $form->setClassAttributeValue('space-y-2 address-form');
        });
    }

    /**
     * Form Modifications: The deprecated AbstractMagewireAddressForm previously included a hard-coded loader
     * notification for specific form field changes. This functionality has now been made backward compatible
     * by setting the loader as a Magewire property.
     *
     * @see AbstractMagewireAddressForm (deprecated)
     */
    private function modifyToBackwardsCompatibleLoaderNotifications()
    {
        // For Component Methods.
        $this->setMagewireProperty('loader', 'submit', __('Saving your address')->render());
        $this->setMagewireProperty('loader', 'edit', __('Preparing address form')->render());
        $this->setMagewireProperty('loader', 'create', __('Preparing form')->render());

        // For Component Data Properties.
        $this->setMagewireProperty('loader', 'data.country_id', __('Switching country')->render());
    }

    /**
     * Form Modifications: Adds default Magewire attributes to all form fields for enhanced interactivity and state management.
     * Sets attributes like `wire:loading` for loading states and generates a unique `wire:key` for tracking changes.
     * Ensures field wrappers are properly configured with unique identifiers during rendering.
     *
     * @see \Hyva\Checkout\Model\Form\EntityFormModifier\MagewireAddressModifier
     */
    private function modifyWithDefaultMagewireAttributes()
    {
        $this->getForm()->modifyFields(function (AbstractEntityField $field) {
            $field->setAttribute('wire:loading.class', 'loading');
            $field->setAttribute('wire:loading.attr', 'disabled');

            $field->setAttribute('wire:target', $field->getTracePath('data'));

            // Generate a unique wire:key value for Magewire to track form changes on the page.
            $key = sprintf(
                '%s-field-wrapper-%s-%s',
                'field-wrapper',
                $this->getForm()->getNamespace(),
                str_replace('.', '-', $field->getTracePath('data'))
            );

            // Assign a unique wire:key for the field wrapper attribute section, if it exists and is being rendered.
            $field->setAttributeForSection('wrapper', 'wire:key', $key);
        });
    }

    /**
     * Form Modifications: Street fields require special handling due to Magento's core requirements. Instead of relying
     * on field names, the implementation must use their positions. This means that if a street field has relatives,
     * its trace path must end with an integer position, and the relative fields must follow the same positional structure.
     */
    private function modifyToStreetFieldsUsingModelDefer(): void
    {
        $this->getForm()->modifyField('street', function (AbstractEntityField $field) {
            $updateWireModelDefer = function (AbstractEntityField $target) {
                $path = $target->getTracePath('data');
                $position = $target->getPosition();

                return preg_match('/(\w+)\.(\1)$/', $path)
                    ? preg_replace('/(\w+)\.(\1)$/', '$1.' . $position, $path)
                    : $path . '.' . $position;
            };

            $applyToStreetField = function (AbstractEntityField $target) use ($updateWireModelDefer) {
                $target->replaceAttribute('wire:model.defer', 'wire:model.defer', $updateWireModelDefer($target));
                $target->setAttribute('wire:target', $target->getAttributeValue('wire:model.defer'));
            };

            if ($field->hasAttribute('wire:model.defer')) {
                $applyToStreetField($field);

                foreach ($field->getRelatives() as $relative) {
                    $applyToStreetField($relative);
                }
            }
        });
    }

    /**
     * Form Modifications: Add a submit and a cancel button to the form when
     * a customer creates or edits an address using the form in a modal.
     */
    private function modifyToIncludeModalSpecificFormElements()
    {
        if (! $this->isModalVisible()) {
            return;
        }

        $this->getForm()->addElement(
            $this->getForm()->createElement('submit', [
                'data' => [
                    'label' => __('Save Address'),
                    'layout_alias' => 'save',
                    'method' => 'save'
                ]
            ])
                ->assignRelative(
                    $this->getForm()->createElement(Button::class, [
                        'data' => [
                            'id' => 'cancel',
                            'label' => __('Cancel'),
                        ]
                    ])
                        ->setClassAttributeValue('btn-secondary')
                        ->setAttribute('x-on:click', 'hyva.modal.pop()')
                )

                ->setAttribute('wire:target', 'trigger(\'save\')')
                ->setAttribute('x-on:click.prevent', 'save')
        );
    }
}
