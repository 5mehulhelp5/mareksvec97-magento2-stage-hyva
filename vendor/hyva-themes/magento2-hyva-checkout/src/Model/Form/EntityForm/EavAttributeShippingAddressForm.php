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
use Hyva\Checkout\Model\Form\EntityField\EavAttributeFieldFactory;
use Hyva\Checkout\Model\Form\EntityFormFieldFactory;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormSaveService\EavAttributeShippingAddress;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

/**
 * @deprecated has been replaced with ShippingDetailsForm.
 * @see ShippingDetailsForm
 */
class EavAttributeShippingAddressForm extends AbstractEntityForm
{
    public const FORM_NAMESPACE = 'shipping';

    protected SessionCheckout $sessionCheckout;
    protected FormFieldMappingManagement $formFieldMappingManagement;

    public function __construct(
        EntityFormFieldFactory $entityFormFieldFactory,
        LayoutInterface $layout,
        LoggerInterface $logger,
        EavAttributeShippingAddress $formSaveService,
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
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function populate(): EntityFormInterface
    {
        if ($this->hasFields()) {
            return $this;
        }

        foreach ($this->formFieldMappingManagement->getShippingFormFieldMapping() as $fieldArguments) {
            $attribute = $fieldArguments['attribute'] ?? null;

            if ($attribute === null) {
                continue;
            }

            try {
                $field = $this->createField(
                    $attribute->getAttributeCode(),
                    $attribute->getFrontendInput(),
                    $fieldArguments,
                    EavAttributeFieldFactory::ACCESSOR
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
        return 'Shipping Address';
    }
}
