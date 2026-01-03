<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView;

use Exception;
use Hyva\Checkout\Exception\CheckoutException;
use Hyva\Checkout\Exception\FormSubmitException;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Checkout as SystemCheckoutConfig;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Hyva\Checkout\Model\Component\AddressTypeManagement;
use Hyva\Checkout\Model\Form\EntityFormModifier\MagewireAddressModifier;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Address\Mapper as MapperAddress;
use Magewirephp\Magewire\Component;
use Psr\Log\LoggerInterface;

/**
 * @deprecated has been replaced with AbstractForm.
 * @see \Hyva\Checkout\Magewire\Component\AbstractForm
 */
abstract class AbstractMagewireAddressForm extends Component implements MagewireAddressFormInterface, EvaluationInterface
{
    public int $autoSaveTimeout = 3000;

    public array $address = [
        'save' => false
    ];

    public array $modal = [
        'shown' => false
    ];

    /**
     * @deprecated no longer supported.
     * @see self::getSaveDateTime()
     */
    public ?string $saveDateTime = null;

    protected MapperAddress $mapperAddress;
    protected MagewireAddressModifier $magewireAddressModifier;
    protected AddressTypeManagement $addressTypeManagement;
    protected SessionCheckout $sessionCheckout;
    protected LoggerInterface $logger;
    protected SystemCheckoutConfig $systemCheckoutConfig;

    protected $listeners = [
        'edit',
        'create',
    ];

    protected $loader = [
        'submit' => 'Saving your address',
        'edit' => 'Preparing address form',
        'create' => 'Preparing form',
        'address.country_id' => 'Switching country'
    ];

    private EntityFormInterface $form;
    private bool $saved = false;

    public function __construct(
        MapperAddress $mapperAddress,
        MagewireAddressModifier $magewireAddressModifier,
        AddressTypeManagement $addressTypeManagement,
        SessionCheckout $sessionCheckout,
        LoggerInterface $logger,
        SystemCheckoutConfig $systemCheckoutConfig
    ) {
        $this->mapperAddress = $mapperAddress;
        $this->magewireAddressModifier = $magewireAddressModifier;
        $this->addressTypeManagement = $addressTypeManagement;
        $this->sessionCheckout = $sessionCheckout;
        $this->logger = $logger;
        $this->systemCheckoutConfig = $systemCheckoutConfig;
    }

    public function boot(): void
    {
        $this->form = $this->getAddressType()->getForm()->init();

        $addressType = $this->getAddressType();
        $addressExport = $addressType->getQuoteAddress()->exportCustomerAddress();

        $address = $this->mapperAddress->toFlatArray($addressExport);
        $address['id'] = $addressExport->getCustomerId();

        // By default, the form is always filled with the current active address.
        $this->getForm()->fill($address);

        // Dispatch form modification event for: form:boot.
        $this->getForm()->dispatchModificationHook('form:boot', [$this]);
        // Dispatch form modification event for: form:{form_namespace}:boot.
        $this->getForm()->dispatchModificationHook(sprintf('form:%s:boot', $addressType), [$this]);

        // Bind all field and nested field values to the address public property.
        $this->address = $this->getForm()->toArray();

        $this->autoSaveTimeout = (int) $this->systemCheckoutConfig->getGroupValue('shipping_billing_form/autosave_timeout', 'developer') ?? 3000;
    }

    public function updatingAddress(array $address): array
    {
        // Reset the form (to remove the current address) and then fill it again to set the value for the address property.
        return $this->getForm()->reset()->fill($address)->toArray();
    }

    public function updated($value, string $name)
    {
        $entity = strpos($name, 'address.') === 0 ? substr($name, strlen('address.')) : $name;
        $field = $this->getForm()->getFieldByPath($entity);

        if ($field === null) {
            return $value;
        }

        /*
         * Executed during each address entity sync, this function runs as the entities are synchronized in an undetermined order.
         * It's important to consider that other fields may not yet have been updated before running a modifier.
         */
        $field->setValue($value);

        // Dispatch form modification event for: form:field:updated.
        $this->getForm()->dispatchModificationHook('form:field:updated', [$field, $this, $this->getAddressType()]);
        // Dispatch form modification event for: form:{field_name}:updated.
        $this->getForm()->dispatchModificationHook(sprintf('form:%s:updated', $entity), [$field, $this]);
        // Dispatch form modification event for: form:{form_namespace}:{field_name}:updated.
        $this->getForm()->dispatchModificationHook(sprintf('form:%s:%s:updated', $this->getAddressType(), $entity), [$field, $this]);

        return $field->getValue();
    }

    public function updatedAddress(array $address): array
    {
        /*
         * After all individual address entities have been synced, this method runs. For form modifications that depend on
         * multiple address changes, it is best to utilize the form modification hooks available in this method.
         */

        // Dispatch form modification event for: form:updated.
        $this->getForm()->dispatchModificationHook('form:updated', [$this]);
        // Dispatch form modification event for: form:{form_namespace}:updated.
        $this->getForm()->dispatchModificationHook(sprintf('form:%s:updated', $this->getAddressType()), [$this]);

        return $this->getForm()->toArray();
    }

    public function store(): bool
    {
        if ($this->getRequest()->isPreceding()) {
            return false;
        }

        $this->getForm()->fill($this->address);
        return $this->save();
    }

    public function submit(): bool
    {
        try {
            $this->getForm()->fill($this->address);

            $save = $this->save();

            if ($save) {
                $this->dispatchSuccessMessage('Address successfully saved.');
            }

            $this->emitByAddressType('%s_address_submitted', ['result' => $save]);
        } catch (Exception $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving the address.');
        } finally {
            $this->hideFormModal();
        }

        return $save ?? false;
    }

    public function save(bool $auto = false): bool
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $previousAddressesCount = count($quote->getCustomer()->getAddresses() ?? []);

            $submit = $this->getForm()->submit();

            if ($submit === false) {
                throw new CheckoutException(
                    __('An error occurred while processing your form. Please try again later.')
                );
            }

            if ($previousAddressesCount < $this->getAddressType()->getCustomerAddressList()->getTotalCount()) {
                $this->emitByAddressType('%s_address_added');
            }

            $this->emitByAddressType('%s_address_saved');
            $this->saved = true;
        } catch (CheckoutException | FormSubmitException $exception) {
            if ($auto === false) {
                $this->dispatchErrorMessage($exception->getMessage());
            }
        } catch (Exception $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving the address.');
        }

        return $submit ?? false;
    }

    public function autosave()
    {
        // Flag to avoid a second save during component dehydration (deprecated).
        $this->saved = true;

        try {
            $submit = $this->getForm()->submit();

            if ($submit === false) {
                $this->emitByAddressType('%s_address_saved');

                throw new CheckoutException(
                    __('An error occurred while processing your form. Please try again later.')
                );
            }
        } catch (CheckoutException | FormSubmitException $exception) {
            $this->dispatchErrorMessage($exception->getMessage());
        } catch (Exception $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving the address.');
        }
    }

    public function edit(): void
    {
        $this->reset();

        try {
            $form = $this->getForm();
            $addressType = $this->getAddressType();
            $address = $addressType->getQuoteAddress()->exportCustomerAddress();

            $this->showFormModal();

            $form->dispatchModificationHook('form:action:edit', [$address]);
            $form->dispatchModificationHook(sprintf('form:%s:action:edit', $addressType), [$address, $this]);

            $this->address = $this->getForm()->toArray();
            $this->address['id'] = $address->getId();
        } catch (Exception $exception) {
            $this->dispatchErrorMessage('Something went wrong.');
        }
    }

    public function create(): void
    {
        $form = $this->getForm()->reset();
        $addressType = $this->getAddressType();

        $this->showFormModal();

        $form->dispatchModificationHook('form:action:create');
        $form->dispatchModificationHook(sprintf('form:%s:action:create', $addressType), [$this]);

        $this->address = $form->toArray();
    }

    public function getForm(): EntityFormInterface
    {
        return $this->form;
    }

    public function getPublicForm(bool $build = true): EntityFormInterface
    {
        $form = $this->getForm();

        if ($build) {
            $form->build()
                 ->dispatchModificationHook('form:build:magewire', [$this, $this->getAddressType()])
                 ->dispatchModificationHook(sprintf('form:%s:build:magewire', $this->getAddressType()), [$this]);
        }

        return $form;
    }

    public function showFormModal(): void
    {
        $this->modal['shown'] = true;

        $this->dispatchBrowserEvent('address-form-modal-show', ['type' => $this->getAddressType()->__toString()]);
    }

    public function hideFormModal(): void
    {
        if ($this->modal['shown'] === false) {
            return;
        }

        $this->modal['shown'] = false;

        $this->dispatchBrowserEvent('address-form-modal-hide', ['type' => $this->getAddressType()->__toString()]);
    }

    /**
     * Emit address type event.
     */
    public function emitByAddressType(string $format, array $params = [])
    {
        $event = sprintf($format, $this->getAddressType());

        try {
            $this->emit(($this->sessionCheckout->getQuote()->getCustomerIsGuest() ? 'guest_' : 'customer_') . $event, $params);
            $this->emit($event, $params);
        } catch (NoSuchEntityException | LocalizedException $exception) {
            $this->logger->info(sprintf('Address type emit failed because: %s', $exception->getMessage()), ['exception' => $exception]);
        }
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        return $resultFactory->createValidation('magewire-form')
            ->withDetails([
                'saveAction' => 'autosave'
            ])
            ->withAlias('submit')
            ->withStackPosition(100);
    }

    /**
     * @doc https://github.com/magewirephp/magewire/blob/main/docs/Features.md#lifecycle-hooks-1
     */
    public function dehydrate(): void
    {
        if ($this->getRequest()->isSubsequent() && ! $this->isFormModalActive() && ! $this->saved) {
            $data = $this->getRequest()->getServerMemo('data');

            // Save the address properties when one or more changed during a subsequent (update) request.
            if (isset($data['address']) && $this->hasAddressChanges($data['address'], $this->getForm()->toArray())) {
                $this->save();
            }
        }
    }

    public function setMagewireProperty(string $subject, string $name, $value): self
    {
        if ($this->isWritableMagewireProperty($subject)) {
            $this->{$subject}[$name] = $value;
        }

        return $this;
    }

    public function getMagewireProperty(string $subject, string $name, $default = null)
    {
        return $this->{$subject}[$name] ?? $default;
    }

    private function isWritableMagewireProperty(string $property): bool
    {
        return in_array($property, ['aliases', 'listeners', 'loader', 'rules', 'messages'])
            && property_exists($this, $property)
            && is_array($this->{$property});
    }

    protected function isFormModalActive(): bool
    {
        return isset($this->modal['shown']) && $this->modal['shown'] === true;
    }

    protected function hasAddressChanges($from, $to): bool
    {
        if (is_array($from) && is_array($to)) {

            $keysFrom = array_keys($from);
            $keysTo = array_keys($to);

            asort($keysFrom);
            asort($keysTo);

            if ($keysFrom !== $keysTo) {
                return true;
            }

            foreach ($keysTo as $key) {
                if ($this->hasAddressChanges($from[$key], $to[$key])) {
                    return true;
                }
            }

            return false;
        }

        return $from !== $to;
    }

    /**
     * @deprecated Property no longer supported for this component.
     */
    public function getSaveDateTime(): ?string
    {
        $change = $this->saveDateTime;

        if (is_string($change)) {
            $change = date('d/m/Y - H:i', strtotime($change)) ?? null;
        }

        return $change;
    }

    /**
     * @deprecated Magewire's new propertyUpdating and propertyUpdated hook methods, make finding
     *             differences obsolete. Also, each single property hook handles its own tasks.
     */
    public function getDifferences(array $array1, array $array2): array
    {
        $result = [];

        foreach ($array1 as $key => $value) {
            if (is_array($value) && isset($array2[$key]) && is_array($array2[$key])) {
                $recursiveDifferences = $this->getDifferences($value, $array2[$key]);

                if (! empty($recursiveDifferences)) {
                    $result[$key] = $recursiveDifferences;
                }
            } else {
                if (array_key_exists($key, $array2) && $array2[$key] != $value) {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @deprecated Address population happens either through the boot process on preceding requests or on subsequent requests
     *             via triggering the address property hook methods.
     */
    public function populateAddressProperty(): array
    {
        return $this->address;
    }
}
