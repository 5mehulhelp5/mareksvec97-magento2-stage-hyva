<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

use Exception;
use Hyva\Checkout\Exception\FormException;
use Hyva\Checkout\Exception\FormSubmitException;
use Hyva\Checkout\Model\Form\EntityConcern\WithAttributes;
use Hyva\Checkout\Model\Form\EntityConcern\WithClassAttribute;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityFormElement\Submit;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractEntityForm implements EntityFormInterface
{
    use WithAttributes;
    use WithClassAttribute;

    protected EntityFormSaveServiceInterface $formSaveService;
    protected LoggerInterface $logger;
    protected JsonSerializer $jsonSerializer;

    /** @deprecated use the $factories array instead, to register required factories. */
    protected EntityFormFieldFactory $entityFormFieldFactory;
    /** @deprecated layout should only be used in renderers. */
    protected LayoutInterface $layout;

    /** @var EntityFormModifierInterface[]|AbstractEntityFormModifier[] */
    protected array $entityFormModifiers;
    /** @var array<string, object> */
    protected array $factories;

    /** @var EntityFormElementInterface[] */
    private array $elements = [];
    // Form elements with the intention to be removed.
    private array $prune = [];
    // Modification callables for specific events.
    private array $modificationsListeners = [];
    private array $modificationsListenersNameMapping = [];

    public function __construct(
        EntityFormFieldFactory $entityFormFieldFactory,
        LayoutInterface $layout,
        LoggerInterface $logger,
        EntityFormSaveServiceInterface $formSaveService,
        JsonSerializer $jsonSerializer,
        array $entityFormModifiers = [],
        array $factories = []
    ) {
        $this->formSaveService = $formSaveService;
        $this->logger = $logger;
        $this->jsonSerializer = $jsonSerializer;
        $this->entityFormModifiers = $entityFormModifiers;
        $this->factories = $factories;

        $this->entityFormFieldFactory = $entityFormFieldFactory;
        $this->layout = $layout;
    }

    abstract public function populate(): EntityFormInterface;

    abstract public function getTitle(): string;

    public function init(): self
    {
        $this->populate();

        $modifiers = array_filter($this->entityFormModifiers, function ($modifier) {
            return ($modifier instanceof EntityFormModifierInterface) || ($modifier instanceof AbstractEntityFormModifier);
        });

        foreach ($modifiers as $modifier) {
            try {
                $modifier->apply($this);
            } catch (Exception $exception) {
                $this->logger->critical(
                    sprintf('Form modifier "%s" threw an exception: %s', get_class($modifier), $exception->getMessage()),
                    ['exception' => $exception]
                );
            }
        }

        $this->dispatchModificationHook('form:init');
        $this->dispatchModificationHook('form:populate');

        return $this;
    }

    public function build(): self
    {
        $this->dispatchModificationHook('form:build');

        $this->elements = array_filter($this->elements, function (EntityFormElementInterface $element) {
            return ! in_array($element->getId(), $this->prune);
        });

        $this->prune = [];
        return $this;
    }

    public function fill(array $values, array $fields = []): self
    {
        $fields = empty($fields) ? $this->getFields() : $fields;

        foreach ($values as $key => $value) {
            $field = $fields[$key] ?? false;

            if ($field === false) {
                continue;
            }

            if (is_array($value)) {
                if ($field->hasRelatives()) {

                    // Relatives are already sorted at this stage.
                    $relatives = $field->getRelatives();
                    array_unshift($relatives, $field);

                    $this->fill($value, $relatives);
                    continue;

                } else {
                    $value = implode(',', $value);
                }
            }

            $field->setData('value', $value);
        }

        /** @deprecated triggered in multiple locations, potentially leading to significant computational overhead. */
        $this->dispatchModificationHook('form:fill', [$values]);

        return $this;
    }

    public function clear(?array $fields = null): EntityFormInterface
    {
        foreach ($fields ?? $this->getFields() as $field) {
            $field->clear();
        }

        return $this;
    }

    /**
     * @return AbstractEntityForm
     */
    public function reset(?array $fields = null): EntityFormInterface
    {
        foreach ($fields ?? $this->getFields() as $field) {
            $field->reset();
        }

        return $this;
    }

    public function sort(): self
    {
        uasort($this->elements, static function (EntityFormElementInterface $a, EntityFormElementInterface $b) {
            return $a->getSortOrder() - $b->getSortOrder();
        });

        return $this;
    }

    public function submit(): bool
    {
        try {
            $this->formSaveService->save($this);
        } catch (Exception $exception) {
            throw new FormSubmitException(__($exception->getMessage()));
        }

        return true;
    }

    public function group(array $elements, EntityFormElementInterface $parent): EntityFormInterface
    {
        foreach ($elements as $element) {
            if (is_string($element)) {
                if (! $this->hasElement($element)) {
                    continue;
                }

                $element = $this->getElement($element);
            }

            $parent->assignRelative($element);

            if ($parent->getId() !== $element->getId()) {
                $this->removeElement($element);
            }
        }

        return $this;
    }

    public function modify(callable $modifier): self
    {
        $modifier($this);

        return $this;
    }

    public function hasFields(): bool
    {
        return count($this->getFields()) !== 0;
    }

    public function getFields(): array
    {
        return array_filter($this->elements, static function ($field) {
            return $field instanceof EntityFieldInterface;
        });
    }

    /**
     * @return AbstractEntityField ()
     */
    public function getField(string $name): ?EntityFieldInterface
    {
        $fields = $this->getFields();
        return $fields[$name] ?? null;
    }

    public function getFieldByPath(string $path, string $separator = '.'): ?EntityFieldInterface
    {
        $path = explode($separator, $path);

        if (count($path) === 1) {
            return $this->getField($path[0]);
        }

        $parent = null;

        foreach ($path as $key => $field) {
            if ($key === 0) {
                $parent = $this->getField($field);
            } elseif ($parent && $parent->hasRelatives()) {
                /** @var EntityFieldInterface[] $relatives */
                $relatives = $parent->getRelatives();

                if (is_numeric($field) && (int) $field === 0) {
                    return $parent;
                }

                if (isset($relatives[$field])) {
                    $parent = $relatives[$field];

                    if ($field === end($path)) {
                        return $parent;
                    }
                }
            }
        }

        return null;
    }

    public function addField(EntityFieldInterface $field): EntityFormInterface
    {
        return $this->addElement($field);
    }

    public function removeField(EntityFieldInterface $field): EntityFormInterface
    {
        return $this->removeElement($field);
    }

    public function hasField(string $name): bool
    {
        return $this->hasElement($name) && $this->getElement($name) instanceof EntityFieldInterface;
    }

    public function setFields(array $fields): EntityFormInterface
    {
        $this->elements = array_filter($fields, static function ($field) {
            return $field instanceof EntityFieldInterface;
        });

        return $this;
    }

    /**
     * @return AbstractEntityFormElement
     */
    public function getElement(string $name): ?EntityFormElementInterface
    {
        return $this->elements[$name] ?? null;
    }

    public function hasElement(string $name): bool
    {
        return array_key_exists($name, $this->elements);
    }

    public function getElements(?callable $callable = null): array
    {
        $callable ??= fn (AbstractEntityFormElement $element) => ! $element instanceof Submit;

        return array_filter($this->elements, $callable);
    }

    public function addElement(EntityFormElementInterface $field): EntityFormInterface
    {
        $this->elements[$field->getId()] = $field;
        return $this;
    }

    public function removeElement(EntityFormElementInterface $field): EntityFormInterface
    {
        $id = $field->getId();

        if (! in_array($id, $this->prune)) {
            $this->prune[] = $id;
        }

        return $this;
    }

    public function reinstateElement(EntityFormElementInterface $element): EntityFormInterface
    {
        $position = array_search($element->getId(), $this->prune);

        if ($position) {
            unset($this->prune[$position]);
        }

        return $this;
    }

    /**
     * @deprecated has been replaced with fields holding their own data.
     */
    public function hasValueData(string $key): bool
    {
        return isset($this->values[$key]);
    }

    public function hasChanges(): bool
    {
        $changes = array_filter($this->getFields(), function (AbstractEntityField $field) {
            return $field->getValue() !== $field->getPreviousValue();
        });

        return count($changes) !== 0;
    }

    public function getNamespace(): string
    {
        return $this::FORM_NAMESPACE;
    }

    public function toArray(array $fields = []): array
    {
        $result = [];
        $fields = empty($fields) ? $this->getFields() : $fields;

        foreach ($fields as $key => $field) {
            if ($field->hasRelatives()) {

                // Relatives first.
                $result[$key] = $this->toArray($field->getRelatives());
                // Unshift the ancestor now it's an array.
                array_unshift($result[$key], $field->getValue());

                continue;

            }

            $result[$key] = $field->getValue();
        }

        return $result;
    }

    public function toJson(): string
    {
        return $this->jsonSerializer->serialize($this->toArray());
    }

    public function isEmpty(): bool
    {
        $fields = $this->getFields();

        $data = array_filter($this->toArray(), static function ($value, $field) use ($fields) {
            return ! empty($value) || $fields[$field]->getDefaultValue() !== $value;
        }, ARRAY_FILTER_USE_BOTH);

        return count($data) === 0;
    }

    /**
     * @throws FormException
     */
    public function createField(string $name, string $type = 'text', array $arguments = [], ?string $withFactory = null): EntityFieldInterface
    {
        $factory = $this->getFactoryFor($withFactory ?? 'fields');

        if (! $factory instanceof EntityFormFieldFactory) {
            throw new FormException(__('Factory "%1" does not implement %2', $withFactory, EntityFormFieldFactory::class));
        }

        return $factory->create($name, $this, $arguments, $type);
    }

    /**
     * Make modifications to a specific field.
     */
    public function modifyField($field, callable $modifier, bool $recursive = true): self
    {
        $field = is_string($field) ? $this->getField($field) : $field;

        if ($field instanceof AbstractEntityFormElement) {
            $field->modify($modifier, $recursive);
        }

        return $this;
    }

    /**
     * Make modifications to all fields at once.
     */
    public function modifyFields(callable $modifier, bool $recursive = true): void
    {
        foreach ($this->getFields() as $field) {
            $this->modifyField($field, $modifier, $recursive);
        }
    }

    /**
     * Make modifications to specific fields by name at once.
     */
    public function modifySpecificFields(array $fields, callable $modifier, bool $recursive = true): void
    {
        $fields = array_filter($this->getFields(), function (AbstractEntityField $field) use ($fields) {
            return in_array($field->getId(), $fields, true);
        });

        foreach ($fields as $field) {
            $this->modifyField($field, $modifier, $recursive);
        }
    }

    /**
     * @throws FormException
     */
    public function createElement(string $name, array $arguments = []): EntityFormElementInterface
    {
        $factory = $this->getFactoryFor('elements');

        if (! $factory instanceof EntityFormFactory) {
            throw new FormException(__('Factory "%1" does not implement %2', 'elements', EntityFormFactory::class));
        }

        return $factory->create($name, $this, $arguments);
    }

    /**
     * Make modifications to a specific element.
     */
    public function modifyElement(string $id, callable $modifier, bool $recursive = true): self
    {
        $element = $this->getElement($id);

        if ($element instanceof AbstractEntityFormElement) {
            $element->modify($modifier, $recursive);
        }

        return $this;
    }

    /**
     * Make modifications to all elements at once.
     */
    public function modifyElements(callable $modifier, bool $recursive = true): void
    {
        foreach ($this->elements as $element) {
            $this->modifyElement($element->getId(), $modifier, $recursive);
        }
    }

    /**
     * Make modifications to specific elements by name at once.
     */
    public function modifySpecificElements(array $elements, callable $modifier, bool $recursive = true): void
    {
        $elements = array_map(
            fn (AbstractEntityFormElement $element) => $element->getId(),
            array_filter($this->elements, function (AbstractEntityFormElement $element) use ($elements) {
                return in_array($element->getId(), $elements, true);
            })
        );

        foreach ($elements as $element) {
            $this->modifyElement($element, $modifier, $recursive);
        }
    }

    public function getFactoryFor(string $name): object
    {
        return $this->factories[$name];
    }

    public function getSaveService(): EntityFormSaveServiceInterface
    {
        return $this->formSaveService;
    }

    /**
     * @throws FormException
     */
    public function registerModificationListener(string $name, string $event, callable $modifier): self
    {
        if (isset($this->modificationsListenersNameMapping[$name])) {
            throw new FormException(__('A form modifier with name "%1" already exists', $name));
        }

        $this->modificationsListeners[$event][$name] = $modifier;
        $this->modificationsListenersNameMapping[$name] = $event;

        return $this;
    }

    public function unregisterModificationListener(string $name): self
    {
        $event = $this->modificationsListenersNameMapping[$name] ?? false;

        if ($event) {
            unset($this->modificationsListeners[$event][$name]);
        }

        return $this;
    }

    public function dispatchModificationHook(string $event, array $args = []): self
    {
        foreach ($this->modificationsListeners[$event] ?? [] as $modification) {
            try {
                $modification($this, ...$args);
            } catch (Exception $exception) {
                $this->logger->critical(
                    sprintf('Form modifier "%s" threw an exception: %s', get_class($exception), $exception->getMessage()),
                    ['exception' => $exception]
                );
            }
        }

        return $this;
    }
}
