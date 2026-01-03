<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;

class CustomConditionFactory
{
    protected ObjectManagerInterface $objectManager;
    protected array $customConditionTypes;

    private array $customConditionModels = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string[] $customConditionTypes
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $customConditionTypes = []
    ) {
        $this->objectManager = $objectManager;
        $this->customConditionTypes = $customConditionTypes;
    }

    /**
     * Create a Custom Condition instance by its class.
     *
     * @param string $type
     * @return CustomConditionInterface
     */
    public function create(string $type): CustomConditionInterface
    {
        $type = $this->customConditionTypes[$type] ?? $type;

        if (!array_key_exists($type, $this->customConditionModels)) {
            if (!class_exists($type)) {
                throw new InvalidArgumentException(
                    sprintf('Class \'%s\' does not exist', $type)
                );
            }
            if (!in_array(CustomConditionInterface::class, class_implements($type), true)) {
                throw new InvalidArgumentException(
                    sprintf('Class \'%s\' does not implement CustomCondition interface', $type)
                );
            }

            $this->customConditionModels[$type] = $this->objectManager->create($type);
        }

        return clone $this->customConditionModels[$type];
    }

    /**
     * Produce multiple Custom Condition instances at once.
     *
     * @param array $types
     * @return array<string, CustomConditionInterface>
     */
    public function produce(array $types): array
    {
        foreach ($types as $key => $type) {
            if ($key !== $type) {
                unset($types[$key]);
            }

            $types[$type] = $this->create($type);
        }

        return $types;
    }
}
