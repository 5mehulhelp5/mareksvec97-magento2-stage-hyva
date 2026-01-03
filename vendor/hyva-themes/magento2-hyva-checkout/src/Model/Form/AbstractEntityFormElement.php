<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

use Hyva\Checkout\Model\Form\EntityConcern\WithAttributes;
use Hyva\Checkout\Model\Form\EntityConcern\WithClassAttribute;
use Hyva\Checkout\Model\Form\EntityFormElement\FormElementDependencies;
use Hyva\Checkout\Model\Form\EntityFormElement\Renderer\AbstractRenderer;
use Hyva\Checkout\Model\Form\EntityFormElement\RendererInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;

abstract class AbstractEntityFormElement extends DataObject implements EntityFormElementInterface
{
    use WithAttributes;
    use WithClassAttribute;

    protected RendererInterface $renderer;
    protected EntityFormInterface $form;

    public function __construct(FormElementDependencies $context, array $data = [])
    {
        parent::__construct($data);

        $this->renderer = $context->getRenderer();
        $this->form = $context->getForm();
    }

    public function getId(): ?string
    {
        return $this->getData(self::ID) ?? uniqid();
    }

    public function getName(): string
    {
        return $this->getData(self::NAME) ?? '';
    }

    public function getLabel(): string
    {
        $label = $this->getData(self::LABEL) ?? '';

        return ($label instanceof Phrase) ? $label->render() : $label;
    }

    public function getClass(array $combineWith = []): string
    {
        return $this->renderClass($combineWith);
    }

    /**
     * Render class attribute value for element.
     */
    public function renderClass(array $combineWith = [], ?string $section = null): string
    {
        $class = $this->getData(self::CLASS_ELEMENT) ?? [];

        if ($section) {
            $class = $class[$section] ?? [];
        }

        if (is_array($class) && ! empty($class)) {
            $combineWith = array_merge($combineWith, $class);
        }

        // Inject the section as a css class itself.
        if ($section && ! in_array($section, $combineWith)) {
            $combineWith[] = $section;
        }

        return preg_replace(
            '/[[:blank:]]+/',
            ' ',
            trim(implode(' ', array_filter($combineWith, fn ($value) => ! is_array($value))))
        );
    }

    public function getLayoutAlias(): ?string
    {
        return $this->getData('layout_alias');
    }

    public function getWrapperClass(array $combineWith = []): array
    {
        $class = array_merge($combineWith, $this->getData(self::CLASS_WRAPPER) ?? []);

        if ($this->hasRelatives()) {
            $class['wrapper-group'] = 'group';
            $class['wrapper-group-id'] = 'group-' . $this->getId();
        }

        return $class;
    }

    public function renderWrapperClass(array $combineWith = []): string
    {
        return trim(implode(' ', $this->getWrapperClass($combineWith)));
    }

    public function getWrapperClasses(array $combineWith = []): array
    {
        return $this->getWrapperClass($combineWith);
    }

    public function canRender(): bool
    {
        return $this->isVisible();
    }

    public function render(): string
    {
        return $this->getRenderer()->render($this);
    }

    /**
     * @return AbstractRenderer
     */
    public function getRenderer(): RendererInterface
    {
        return $this->renderer;
    }

    public function assignAncestor(EntityFormElementInterface $ancestor): self
    {
        $this->setData(self::ANCESTOR, $ancestor);
        return $this;
    }

    public function hasAncestor(): bool
    {
        return $this->hasData(self::ANCESTOR);
    }

    public function hasNamesakeAncestor(): bool
    {
        return $this->hasAncestor() && $this->getAncestor()->getId() === $this->getId();
    }

    public function getAncestor(): ?EntityFieldInterface
    {
        return $this->getData(self::ANCESTOR);
    }

    public function removeAncestor(): self
    {
        $this->unsetData(self::ANCESTOR);
        return $this;
    }

    public function assignRelative(EntityFormElementInterface $relative): self
    {
        // Can't be attached to a form because of it having an ancestor.
        if ($relative->hasAncestor()) {
            $relative->removeAncestor();
        } elseif ($this->getId() !== $relative->getId()) {
            $this->getForm()->removeElement($relative);
        }

        $relative->assignAncestor($this);
        // Move up one level according to its ancestor.
        $relative->setLevel($this->getLevel() + 1);

        $relatives = $this->getRelatives();

        if ($this->getId() === $relative->getId()) {
            // Only increment a fields position when the ancestor and relative share the same name.
            $relative->setPosition(count($relatives) + 1);
            // Making sure the relative ID is set as key and made unique with its position {string.int}.
            $relatives[$relative->getPosition()] = $relative;
        } else {
            $relatives[$relative->getId()] = $relative;
        }

        $this->setData(self::RELATIVES, $relatives);
        return $this;
    }

    public function getRelative(string $id): ?AbstractEntityFormElement
    {
        return $this->getRelatives()[$id] ?? null;
    }

    public function hasRelatives(): bool
    {
        return ! empty($this->getRelatives());
    }

    public function getRelatives(): array
    {
        return $this->getData(self::RELATIVES) ?? [];
    }

    public function hasNamesakeRelatives(): bool
    {
        return count($this->getNamesakeRelatives()) !== 0;
    }

    /**
     * @return EntityFormElementInterface[]
     */
    public function getNamesakeRelatives(): array
    {
        return array_filter(array_keys($this->getRelatives()), function ($key) {
            return is_string($key) ? strpos($key, $this->getId()) === 0 : $key;
        });
    }

    public function removeRelative(EntityFormElementInterface $field): self
    {
        $key = $field->getId();
        $relatives = $this->getRelatives();

        if (isset($relatives[$key])) {
            $relative = $relatives[$key];
            unset($relatives[$key]);

            // Give the field back to its root and set the intention to remove.
            $this->getForm()->addField($relative)->removeElement($relative);
            // Re-assign the leftover relatives.
            $this->setData(self::RELATIVES, $relatives);
        }

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->getPosition();
    }

    public function hasTooltip(): bool
    {
        return ! empty($this->getTooltip());
    }

    public function getTooltip(): string
    {
        return $this->getData(self::TOOLTIP) ?? '';
    }

    public function hasIcon(): bool
    {
        return strlen($this->getIcon()) !== 0;
    }

    public function getIcon(): string
    {
        return $this->getData('icon') ?? '';
    }

    public function hasComment(): bool
    {
        return strlen($this->getComment()) !== 0;
    }

    public function getComment(): string
    {
        return (string) $this->getData(self::COMMENT);
    }

    public function setPosition(int $position): self
    {
        $this->setData(self::POSITION, $position);
        return $this;
    }

    public function getPosition(): int
    {
        return (int) $this->getData(self::POSITION) ?? 0;
    }

    protected function setLevel(int $level): self
    {
        $this->setData(self::LEVEL, $level);
        return $this;
    }

    public function getLevel(): int
    {
        return (int) $this->getData(self::LEVEL) ?? 0;
    }

    public function getForm(): EntityFormInterface
    {
        return $this->form;
    }

    public function enable(): self
    {
        $this->setData(self::STATE, self::STATE_ENABLED);
        return $this;
    }

    public function disable(): self
    {
        $this->setData(self::STATE, self::STATE_DISABLED);
        return $this;
    }

    public function hide(): self
    {
        $this->setData(self::VISIBLE, false);
        return $this;
    }

    public function show(): self
    {
        $this->setData(self::VISIBLE, true);
        return $this;
    }

    public function isVisible(): bool
    {
        return $this->getData(self::VISIBLE) ?? true;
    }

    public function getState(): int
    {
        $state = $this->getData(self::STATE) ?? self::STATE_ENABLED;
        return in_array($state, [self::STATE_ENABLED, self::STATE_DISABLED]) ? $state : self::STATE_DISABLED;
    }

    public function getTracePath(?string $prefix = null, string $delimiter = '.'): string
    {
        $path = [$this->getId()];

        if ($this->hasAncestor()) {
            $path[] = $this->getAncestor()->getTracePath(null, $delimiter);
        }

        if ($prefix) {
            $path[] = $prefix;
        }

        return implode($delimiter, array_reverse($path));
    }

    public function modify(callable $modifier, bool $recursive = true): self
    {
        $modifier($this);

        if ($recursive && $this->hasRelatives()) {
            foreach ($this->getRelatives() as $relative) {
                if ($relative instanceof AbstractEntityFormElement) {
                    $relative->modify($modifier);
                }
            }
        }

        return $this;
    }
}
