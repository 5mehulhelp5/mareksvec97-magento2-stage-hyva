<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

use Hyva\Checkout\Model\Form\EntityField\FormFieldDependencies;
use Hyva\Checkout\Model\Form\EntityFormElement\FormElementDependencies;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Despite its name, this factory does not create form instances, but rather form element instances.
 */
class EntityFormFactory
{
    protected ObjectManagerInterface $objectManager;
    protected LoggerInterface $logger;
    /** @var array<string, string<EntityFormElementInterface>> $customFields */
    protected array $elements;

    public function __construct(
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger,
        array $elements = []
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->elements = $elements;
    }

    public function create(string $name, EntityFormInterface $form, array $arguments = []): EntityFormElementInterface
    {
        // To support backwards compatibility.
        $arguments['form'] = $arguments['form'] ?? $form;

        if (! isset($arguments['data'][EntityFormElementInterface::POSITION])) {
            $arguments['data'][EntityFormElementInterface::POSITION] = 999;
        }
        if (! isset($arguments['data'][EntityFormElementInterface::LABEL])) {
            $arguments['data'][EntityFormElementInterface::LABEL] = $arguments['data']['id'] ?? '';
        }

        if (! isset($arguments['context'])) {
            $arguments['context'] = $this->objectManager->create(FormElementDependencies::class, $arguments);
        } elseif (class_exists($arguments['context'])) {
            $arguments['context'] = $this->objectManager->create($arguments['context'], $arguments);
        }

        $arguments = array_merge($arguments, ['data' => $arguments['data'] ?? null]);
        return $this->objectManager->create($this->elements[$name] ?? $name, $arguments);
    }
}
