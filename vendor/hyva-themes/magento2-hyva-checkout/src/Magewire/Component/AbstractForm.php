<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Component;

use Hyva\Checkout\Exception\FormSubmitException;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityFormElement\Clickable;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Batch as EvaluationResultBatch;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Magewirephp\Magewire\Component;
use Magewirephp\Magewire\Exception\AcceptableException;
use Psr\Log\LoggerInterface;
use Rakit\Validation\Validator;

abstract class AbstractForm extends Component\Form implements EvaluationInterface
{
    public const PROPERTY_ALIASES = 'aliases';
    public const PROPERTY_LISTENERS = 'listeners';
    public const PROPERTY_LOADER = 'loader';
    public const PROPERTY_RULES = 'rules';
    public const PROPERTY_MESSAGES = 'messages';

    // Placeholder for data related to form fields.
    public array $data = [];
    // Placeholder for miscellaneous data that doesn't pertain directly to form fields.
    public array $misc = [];
    // Flag letting the form know it can automatically save after subsequent requests during dehydration.
    public bool $autosave = true;

    protected array $aliases = [];

    protected bool $build = false;
    protected bool $updating = false;

    protected $loader = [];

    /** @use self::getForm instead of $this->form */
    private AbstractEntityForm $form;
    /** @use self::getEvaluationResultBatch instead of $this->form */
    private EvaluationResultBatch $evaluationResultBatch;

    protected LoggerInterface $logger;

    public function __construct(
        Validator $validator,
        AbstractEntityForm $form,
        LoggerInterface $logger,
        EvaluationResultBatch $evaluationResultBatch
    ) {
        parent::__construct($validator);

        $this->form = $form;
        $this->evaluationResultBatch = $evaluationResultBatch;
        $this->logger = $logger;
    }

    /**
     * Boots the Magewire driven form.
     */
    public function boot(): void
    {
        // Initialize to complete the initial form.
        $this->getForm()->init();
        // Perform initial form modifications before any other operations.
        $this->getForm()->modify([$this, 'construct']);

        // Hook: form:boot:magewire
        $this->dispatchFormModificationHook('boot');

        $this->data = $this->getForm()->toArray();
    }

    /**
     * Constructs the base structure of the form by adding fields and elements.
     *
     * Using this method allows you to add elements before any modifications are made,
     * ensuring control over the initial contents of the form.
     *
     * To achieve this via a separate class, use the "form:construct:magewire" modification hook.
     */
    public function construct(AbstractEntityForm $form): void
    {
        // Hook: form:construct:magewire
        $this->dispatchFormModificationHook('construct');
    }

    /**
     * Responsibility to execute both the "mount" Magewire lifecycle and form modification hooks.
     *
     * Note: This is exclusively dispatched during page rendering. In simpler terms, it will not
     *       be triggered when transitioning from one step to another.
     */
    public function mount(): void
    {
        // Hook: form:mount:magewire
        $this->dispatchFormModificationHook('mount');
    }

    /**
     * Responsibility to execute both the "booted" Magewire lifecycle and form modification hooks.
     */
    public function booted(): void
    {
        // Ensure that the form is pre-filled with the previously entered data.
        $this->getForm()->fill($this->data);

        // Hook: form:booted:magewire
        $this->dispatchFormModificationHook('booted');
    }

    /**
     * Responsibility to execute both the "updating" Magewire lifecycle and form modification hooks.
     */
    public function updating($value, string $name)
    {
        // Mark the current subsequent request as an "updating" subsequent request.
        $this->updating = true;

        // Hook: form:updating:magewire
        $this->dispatchFormModificationHook('updating', ['property' => $name, 'value' => $value]);

        return $value;
    }

    /**
     * Responsibility to execute both the "updated" Magewire lifecycle and form modification hooks.
     */
    public function updated($value, string $name)
    {
        // Hook: form:updated:magewire
        $this->dispatchFormModificationHook('updated', ['property' => $name, 'value' => $value]);

        return $value;
    }

    /**
     * Responsibility to execute both the "hydrate" Magewire lifecycle and form modification hooks.
     */
    public function hydrate(): void
    {
        // Hook: form:hydrate:magewire
        $this->dispatchFormModificationHook('hydrate');
    }

    /**
     * Responsibility to execute both the "dehydrate" Magewire lifecycle and form modification hooks.
     *
     * @throws FormSubmitException
     */
    public function dehydrate(): void
    {
        // Hook: form:dehydrate:magewire
        $this->dispatchFormModificationHook('dehydrate');

        /*
         * Automatically trigger a form submission if the current request is a subsequent one
         * and the form fields contain data that has changed compared to their previous values.
         */
        if ($this->canAutoSave()) {
            $this->getForm()->submit();
        }

        $this->data = $this->getForm()->toArray();
    }

    /**
     * Trigger a specific modification hook from the frontend.
     *
     * Example:
     *   <button type="button" wire:click="trigger('start')">Click Me</button>
     *   Will trigger form modification hook "form:{form_namespace}:execute:start".
     */
    public function trigger(string $method, array $args = []): void
    {
        $methods = array_map(
            fn (Clickable $clickable) => $clickable->getMethod(),
            $this->getForm()->getElements(fn (EntityFormElementInterface $element) => $element instanceof Clickable)
        );

        // Attempt to invoke an 'execute:{method}' modification hook upon clicking a Clickable form element.
        if (in_array($method, $methods) && ! in_array($method, get_class_methods($this))) {
            $this->dispatchFormModificationHook(sprintf('execute:%s', $method), $args);
        }
    }

    /**
     * Validate and submit the form data.
     *
     * @throws AcceptableException
     */
    public function submit(?array $data = null, array $rules = [], array $messages = []): bool
    {
        // Form submit always takes the form values as its source of truth.
        $data = $this->getForm()->clear()->fill($data ?? $this->getData())->toArray();

        $rules = array_merge($this->getValidationRules(), $rules);
        $messages = array_merge($this->getValidationMessages(), $messages);

        // Triggers an AcceptableException and halts any subsequent execution of the method.
        $this->validate($rules, $messages, $data, $this->getValidationAliases(), false);
        // We always assume the worst-case scenario.
        $result = false;

        try {
            $result = $this->getForm()->submit();
        } catch (FormSubmitException $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }

        $this->dispatchFormModificationHook('execute:submit', [$result, $data, $exception ?? null]);

        return $result;
    }

    public function build(): AbstractEntityForm
    {
        $form = $this->getForm()->build();
        $evaluationBatch = $this->getEvaluationResultBatch();

        // Initialize a Magewire form upon rendering the component.
        // Template: Hyva_Checkout::page/js/api/v1/alpinejs/magewire-form-component.phtml.
        if (! $form->hasAttribute('x-data')) {
            $form->setAttribute('x-data', 'initMagewireForm');
        }
        if ($form->hasAttribute('x-init')) {
            $form->setAttribute('x-init', 'initialize');
        }

        // Only dispatch a Magewire form modification hook, ensuring backward compatibility
        // since the form built by itself already dispatches a form:build event.
        $this->dispatchFormModificationHook('build');

        $evaluationBatch->misses(fn (EvaluationResult $result) => $result->hasAlias('submit'), function (EvaluationResultBatch $batch) {
            $batch->push(
                $batch->getFactory()
                      ->createValidation('magewire-form')
                      ->withDetails([
                          'saveAction' => 'submit' // Frontend can use "submit" method to save form.
                      ])
                      ->withAlias('submit')
                      ->withStackPosition(100)
            );
        });

        $this->build = true;
        return $form;
    }

    /**
     * Retrieve the populated and build form instance.
     */
    public function getPublicForm(): AbstractEntityForm
    {
        return $this->build ? $this->getForm() : $this->build();
    }

    /**
     * Execute the "updating" data Magewire lifecycle hook.
     */
    public function updatingData(array $data): array
    {
        $form = $this->getForm()->fill($data);

        $this->dispatchFormModificationHook('data:updating');

        // Trigger individual form modification hooks only for the data entries that have been changed.
        foreach ($this->fetchArrayChanges($form->toArray(), $this->data) as $path => $result) {
            $this->dispatchFormModificationHook(sprintf('updating:data:%s', $path), $result);
        }

        return $form->toArray();
    }

    /**
     * Execute the "updated" data Magewire lifecycle hook.
     */
    public function updatedData(array $data): array
    {
        $form = $this->getForm()->fill($data);

        $this->dispatchFormModificationHook('data:updated');

        return $form->toArray();
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Facilitates the modification of specific Magewire properties by adding or revising an array key.
     *
     * Property options:
     *   ['aliases', 'listeners', 'loader', 'rules', 'messages']
     */
    public function setMagewireProperty(string $subject, string $name, $value): self
    {
        if ($this->isWritableMagewireProperty($subject)) {
            $this->{$subject}[$name] = $value;
        }

        return $this;
    }

    /**
     * Retrieves the value of a specific Magewire property by its array key.
     *
     * Property options:
     *   ['aliases', 'listeners', 'loader', 'rules', 'messages']
     */
    public function getMagewireProperty(string $subject, string $name, $default = null)
    {
        return $this->{$subject}[$name] ?? $default;
    }

    /**
     * Enables the removal of an array key to modify specific Magewire properties.
     *
     * Property options:
     *   ['aliases', 'listeners', 'loader', 'rules', 'messages']
     */
    public function removeMagewireProperty(string $subject, string $name): self
    {
        if ($this->isWritableMagewireProperty($subject) && isset($this->{$subject}[$name])) {
            unset($this->{$subject}[$name]);
        }

        return $this;
    }

    public function setValidationRule(string $path, string $rule, ?string $message = null): AbstractForm
    {
        $this->rules[$path] = $rule;

        if ($message) {
            $this->messages[$rule] = $message;
        }

        return $this;
    }

    /**
     * Retrieve evaluation batch instance.
     */
    public function getEvaluationResultBatch(): EvaluationResultBatch
    {
        return $this->evaluationResultBatch;
    }

    /**
     * Retrieve component evaluation completion results.
     */
    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResult
    {
        return $this->getEvaluationResultBatch();
    }

    /**
     * Returns form validation rules.
     *
     * This method was primarily introduced to provide developers with the flexibility to use plugins,
     * allowing them to attach either a before- and/or after-plugin.
     *
     * @see self::setValidationRule()
     */
    public function getValidationRules(array $rules = []): array
    {
        return empty($rules) ? $this->rules : array_merge($this->rules, $rules);
    }

    /**
     * Returns form validation messages.
     *
     * This method was primarily introduced to provide developers with the flexibility to use plugins,
     * allowing them to attach either a before- and/or after-plugin.
     *
     * @see self::setValidationRule()
     */
    public function getValidationMessages(array $messages = []): array
    {
        return empty($messages) ? $this->messages : array_merge($this->messages, $messages);
    }

    /**
     * Returns form validation aliases.
     *
     * This method was primarily introduced to provide developers with the flexibility to use plugins,
     * allowing them to attach either a before- and/or after-plugin.
     */
    public function getValidationAliases(array $aliases = []): array
    {
        return empty($aliases) ? $this->aliases : array_merge($this->aliases, $aliases);
    }

    public function getUncallables(): array
    {
        // All public methods that shouldn't be callable from the frontend.
        $uncallable = [
            'init',
            'updatingData',
            'updatedData',
            'getPublicForm',
            'getData',
            'getRules',
            'getValidationRules',
            'getMessages',
            'getValidationMessages',
            'getAliases',
            'getValidationAliases',
            'setValidationRule',
            'getEvaluationResultBatch',
            'setEvaluation',
            'setValidationRule',
            'setMagewireProperty',
            'removeMagewireProperty',
            'getUncallables',
        ];

        return empty($this->uncallables) ? $uncallable : array_merge($this->uncallables, $uncallable);
    }

    /**
     * Returns if the current request is an "updating" subsequent request.
     */
    public function isUpdating(): bool
    {
        return $this->updating;
    }

    /**
     * Retrieve the populated form instance.
     */
    protected function getForm(): AbstractEntityForm
    {
        return $this->form;
    }

    protected function canAutoSave(): bool
    {
        return $this->getRequest()->isSubsequent()
            && $this->autosave
            && $this->updating
            && $this->getForm()->hasChanges();
    }

    /**
     * Dispatches a Magewire-specific modification hook event for situations driven by Magewire
     * which would not occur on non-Magewire-driven form objects.
     *
     * Best practice dictates utilizing Magewire hook events exclusively for Magewire-driven forms.
     *
     * Example:
     *   - form:mount:magewire
     */
    protected function dispatchFormModificationHook(string $hook, array $args = []): AbstractEntityForm
    {
        return $this->getForm()->dispatchModificationHook(
            sprintf('form:%s:magewire', $hook),
            array_merge([$this], $args)
        );
    }

    private function isWritableMagewireProperty(string $property): bool
    {
        return in_array($property, ['aliases', 'listeners', 'loader', 'rules', 'messages'])
            && property_exists($this, $property)
            && is_array($this->{$property});
    }

    /**
     * Recursively compares $array1 and $array2, returning all detected changes.
     */
    private function fetchArrayChanges(array $array1, array $array2, string $prefix = ''): array
    {
        $diff = [];

        foreach ($array2 as $key => $value) {
            $path = $prefix ? $prefix . ':' . $key : $key;

            if (! array_key_exists($key, $array1)) {
                $diff[$path] = $value;
            } elseif (is_array($value) && is_array($array1[$key])) {
                $nested = $this->fetchArrayChanges($array1[$key], $value, $path);

                foreach ($nested as $nestedKey => $nestedValue) {
                    $diff[$nestedKey] = $nestedValue;
                }
            } elseif ($array1[$key] !== $value) {
                $diff[$path] = [
                    'path' => $path,
                    'from' => $value,
                    'to'   => $array1[$key]
                ];
            }
        }

        return $diff;
    }
}
