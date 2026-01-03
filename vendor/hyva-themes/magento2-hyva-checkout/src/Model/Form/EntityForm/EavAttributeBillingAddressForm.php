<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityForm;

use Hyva\Checkout\Exception\FormException;
use Hyva\Checkout\Model\ConfigData\FormFieldMappingManagement;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityFormFieldFactory;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormSaveService\EavAttributeBillingAddress;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

class EavAttributeBillingAddressForm extends AbstractEntityForm
{
    public const FORM_NAMESPACE = 'billing';

    protected FormFieldMappingManagement $formFieldMappingManagement;
    protected SessionCheckout $sessionCheckout;

    public function __construct(
        EntityFormFieldFactory $entityFormFieldFactory,
        LayoutInterface $layout,
        LoggerInterface $logger,
        EavAttributeBillingAddress $formSaveService,
        JsonSerializer $jsonSerializer,
        FormFieldMappingManagement $formFieldMappingManagement,
        SessionCheckout $sessionCheckout,
        array $entityFormModifiers = [],
        array $factories = []
    ) {
        parent::__construct(
            $entityFormFieldFactory,
            $layout,
            $logger,
            $formSaveService,
            $jsonSerializer,
            $entityFormModifiers,
            $factories
        );

        $this->formFieldMappingManagement = $formFieldMappingManagement;
        $this->sessionCheckout = $sessionCheckout;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function populate(): EntityFormInterface
    {
        if ($this->hasFields()) {
            return $this;
        }

        foreach ($this->formFieldMappingManagement->getBillingAddressMapping() as $fieldArguments) {
            $attribute = $fieldArguments['attribute'] ?? null;

            if ($attribute === null) {
                continue;
            }

            try {
                $field = $this->createField(
                    $attribute->getAttributeCode(),
                    $attribute->getFrontendInput(),
                    $fieldArguments,
                    'eav_fields'
                );

                if ($this->hasField($attribute->getAttributeCode())) {

                    // For those cases where there are duplicate attribute codes in the mapping.
                    $this->group([$field], $this->getField($attribute->getAttributeCode()));
                    continue;

                }

                $this->addField($field);
            } catch (FormException $exception) {
                $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $this;
    }

    public function getTitle(): string
    {
        return 'Billing Address';
    }
}
