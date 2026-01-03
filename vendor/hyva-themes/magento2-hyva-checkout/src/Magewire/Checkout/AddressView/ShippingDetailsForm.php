<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView;

use Exception;
use Hyva\Checkout\Exception\FormException;
use Hyva\Checkout\Magewire\Component\AbstractAddressForm;
use Hyva\Checkout\Model\Component\AddressTypeInterface;
use Hyva\Checkout\Model\Form\AbstractEntityForm;

class ShippingDetailsForm extends AbstractAddressForm
{
    public function construct(AbstractEntityForm $form): void
    {
        try {
            $this->getForm()->registerModificationListener(
                '@internal_modifyToUseTheAlpineJsShippingFormShippingDetails',
                'form:build:magewire',
                fn () => $this->modifyToUseTheAlpineJsShippingFormShippingDetails()
            );
            $this->getForm()->registerModificationListener(
                '@internal_modifyToUseHiddenAddressIdentifierField',
                'form:construct:magewire',
                fn () => $this->modifyToUseHiddenAddressIdentifierField()
            );
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }

        $addressType = $this->getAddressType();
        $addressExport = $addressType->getQuoteAddress()->exportCustomerAddress();

        $address = $this->getAddressMapper()->toFlatArray($addressExport);
        $address['id'] = $addressExport->getCustomerId();

        // Handles form population based on the selected "shipping" address type.
        parent::construct($form->fill($address));
    }

    protected function getAddressType(): AddressTypeInterface
    {
        return $this->getAddressTypeManagement()->getAddressTypeShipping();
    }

    /**
     * Form Modifications: By default this abstraction transforms the form into an AlpineJS component using
     * initMagewireForm. This needs to be tweaked to make shipping specific modifications.
     */
    private function modifyToUseTheAlpineJsShippingFormShippingDetails()
    {
        $this->getForm()->modify(function (AbstractEntityForm $form) {
            $form->setAttribute('x-data', 'initMagewireFormForShippingDetails($el, $wire)');
        });
    }

    /**
     * @throws FormException
     */
    private function modifyToUseHiddenAddressIdentifierField()
    {
        $this->getForm()->addField(
            $this->getForm()->createField('id', 'hidden')
        );
    }
}
