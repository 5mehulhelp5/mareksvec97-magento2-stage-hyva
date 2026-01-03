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
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;

class EntityFormFieldFactory extends EntityFormFactory implements EntityFormFieldFactoryInterface
{
    protected ObjectManagerInterface $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger,
        array $elements = [],
        array $customFields = []
    ) {
        $this->objectManager = $objectManager;

        parent::__construct($objectManager, $logger, array_merge_recursive($elements, $customFields));
    }

    public function create(string $name, EntityFormInterface $form, array $arguments = [], ?string $type = null): EntityFieldInterface
    {
        if (! isset($arguments['context'])) {
            $arguments['context'] = FormFieldDependencies::class;
        }

        if (! isset($arguments['data'][EntityFormElementInterface::ID])) {
            $arguments['data'][EntityFormElementInterface::ID] = $name;
        }
        if (! isset($arguments['data'][EntityFormElementInterface::NAME])) {
            $arguments['data'][EntityFormElementInterface::NAME] = $name;
        }
        if (! isset($arguments['data'][EntityFieldInterface::INPUT])) {
            $arguments['data'][EntityFieldInterface::INPUT] = $type;
        }

        /** @var EntityFieldInterface $field */
        $field = parent::create($this->resolveClassType($name, $type, $form), $form, $arguments);

        return $field;
    }

    public function resolveClassType(string $id, string $type, EntityFormInterface $form): string
    {
        $variants = $this->getClassTypes($id, $type, $form);

        foreach ($variants as $variant) {
            $variant = $this->elements[$variant] ?? false;

            if ($variant) {
                return $variant;
            }
        }

        return EntityFieldInterface::class;
    }

    /**
     * @return string[]
     */
    public function getClassTypes(string $name, string $type, EntityFormInterface $form): array
    {
        $form = $form->getNamespace();

        return [
            $form . '.' . $name . '.' . $type,  // form-name.field-name.input-type
            $name . '.' . $type,                // field-name.input-type
            $name,                              // field-name
            $form . '.' . $type,                // form-name.type
            $type                               // type
        ];
    }
}
