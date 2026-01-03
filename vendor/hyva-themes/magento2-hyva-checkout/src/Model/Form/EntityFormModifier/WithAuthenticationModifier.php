<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Exception\FormException;
use Hyva\Checkout\Model\ConfigData\FormFieldMappingManagement;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigAddressForm;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityFieldConfigInterfaceFactory;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;

class WithAuthenticationModifier implements EntityFormModifierInterface
{
    protected SessionCustomer $sessionCustomer;
    protected AttributeManagementInterface $attributeManagement;
    protected FormFieldMappingManagement $formFieldMappingManagement;
    protected EntityFieldConfigInterfaceFactory $entityFieldConfigInterfaceFactory;
    protected SystemConfigAddressForm $systemConfigShippingAddressForm;
    protected SessionCheckout $sessionCheckout;
    protected LoggerInterface $logger;

    public function __construct(
        SessionCustomer $sessionCustomer,
        FormFieldMappingManagement $formFieldMappingManagement,
        EntityFieldConfigInterfaceFactory $entityFieldConfigInterfaceFactory,
        SystemConfigAddressForm $systemConfigShippingAddressForm,
        ?SessionCheckout $sessionCheckout = null,
        ?LoggerInterface $logger = null
    ) {
        $this->sessionCustomer = $sessionCustomer;
        $this->formFieldMappingManagement = $formFieldMappingManagement;
        $this->entityFieldConfigInterfaceFactory = $entityFieldConfigInterfaceFactory;
        $this->systemConfigShippingAddressForm = $systemConfigShippingAddressForm;

        $this->sessionCheckout = $sessionCheckout
            ?: ObjectManager::getInstance()->get(SessionCheckout::class);
        $this->logger = $logger
            ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        if (! $this->sessionCustomer->isLoggedIn()) {
            return $form;
        }

        return $form->registerModificationListener(
            'applyCustomerElements',
            'form:populate',
            fn (AbstractEntityForm $form) => $this->applyCustomerAddressSaveOption($form)
        );
    }

    /**
     * @deprecated Extracted the email field into its own dedicated component, allowing it to be removed from any address form.
     * @see \Hyva\Checkout\Magewire\Checkout\GuestDetails
     */
    public function applyCustomerElements(EntityFormInterface $form): EntityFormInterface
    {
        return $form;
    }

    /**
     * @deprecated Extracted the email field into its own dedicated component, allowing it to be removed from any address form.
     * @see \Hyva\Checkout\Magewire\Checkout\GuestDetails
     */
    public function applyEmailTooltip(EntityFormInterface $form): EntityFormInterface
    {
        return $form;
    }

    /**
     * @throws FormException
     */
    private function applyCustomerAddressSaveOption(AbstractEntityForm $form): AbstractEntityForm
    {
        // Adds the "Save address in address book" option to the end of the form when it's a logged in customer.
        $form->addField(
            $form->createField('save')
                ->setData('position', 900)
                ->setLabel(__('Save in address book'))
        );

        return $form;
    }
}
