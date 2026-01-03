<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Checkout;

use Magento\Framework\App\ObjectManager;

class Step
{
    public const UPDATE_TYPE_LAYOUT  = 'layout';
    public const UPDATE_TYPE_DEFAULT = 'default';
    public const UPDATE_TYPE_CUSTOM  = 'custom';

    private string $name  = 'unknown';
    private string $label = 'Unknown';
    private string $route = 'unknown';

    private array $events = [];
    private int $position = 0;

    private ?StepConditions $conditions = null;
    private StepConditionsFactory $stepConditionsFactory;

    private ?StepLayout $layout = null;
    private StepLayoutFactory $stepLayoutFactory;

    public function __construct(
        StepConditionsFactory $stepConditionsFactory,
        ?StepLayoutFactory $stepLayoutFactory = null
    ) {
        $this->stepConditionsFactory = $stepConditionsFactory;

        $this->stepLayoutFactory = $stepLayoutFactory
            ?: ObjectManager::getInstance()->get(StepLayoutFactory::class);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $label
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = ucfirst($label);

        return $this;
    }

    /**
     * @param string|null $default
     * @return string
     */
    public function getLabel(?string $default = null): string
    {
        return $this->label ?? $default;
    }

    /**
     * @param string $route
     * @return $this
     */
    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Setter wrapper for setting updates (required).
     * It's recommended to use getLayout()->setUpdates().
     *
     * @param mixed $updates
     * @return $this
     */
    public function setUpdates(array $updates): self
    {
        $this->getLayout()->setUpdates($updates);

        return $this;
    }

    /**
     * Getter wrapper for getting updates.
     *
     * It's recommended to use getLayout()->getUpdates().
     *
     * @return mixed
     * @see self::getLayout()
     */
    public function getUpdates(?string $type = null): array
    {
        $updates = $this->getLayout()->getUpdates();

        if ($type) {
            return $updates[$type] ?? [];
        }

        return $updates;
    }

    /**
     * @return StepLayout
     */
    public function getLayout(): StepLayout
    {
        if ($this->layout === null) {
            $this->layout = $this->stepLayoutFactory->create();
        }

        return $this->layout;
    }

    /**
     * Setter wrapper for setting conditions.
     *
     * @param mixed $conditions
     * @return $this
     */
    public function setConditions(?array $conditions): self
    {
        $this->getConditions()->setItems($conditions);

        return $this;
    }

    /**
     * Getter wrapper for getting conditions.
     *
     * @return mixed
     */
    public function getConditions()
    {
        if ($this->conditions === null) {
            $this->conditions = $this->stepConditionsFactory->create();
        }

        return $this->conditions;
    }

    /**
     * @param mixed $events
     * @return $this
     */
    public function setEvents(array $events): self
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function canInclude(): bool
    {
        /** @var StepConditions $conditions */
        $conditions = $this->getConditions();

        if ($conditions === null) {
            return true;
        }

        return $conditions->assertSuccess();
    }

    public function validate(): bool
    {
        return true;
    }

    public function toPublicDataArray(): array
    {
        return [
            'name' => $this->getName(),
            'route' => $this->getRoute(),
            'label' => $this->getLabel(),
            'position' => $this->getPosition()
        ];
    }

    public function __toString(): string
    {
        return $this->getLabel();
    }
}
