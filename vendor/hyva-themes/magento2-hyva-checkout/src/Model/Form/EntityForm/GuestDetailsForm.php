<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityForm;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGuestDetails;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityFormFieldFactory;
use Hyva\Checkout\Model\Form\EntityFormSaveService\GuestDetails as GuestDetailsFormSaveService;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

class GuestDetailsForm extends AbstractEntityForm
{
    public const FORM_NAMESPACE = 'guest_details';
    public const FIELD_EMAIL = 'email_address';

    private SessionCheckout $sessionCheckout;
    private SystemConfigGuestDetails $systemConfigGuestDetails;

    public function __construct(
        EntityFormFieldFactory $entityFormFieldFactory,
        LayoutInterface $layout,
        LoggerInterface $logger,
        GuestDetailsFormSaveService $formSaveService,
        JsonSerializer $jsonSerializer,
        SessionCheckout $sessionCheckout,
        SystemConfigGuestDetails $systemConfigGuestDetails,
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

        $this->sessionCheckout = $sessionCheckout;
        $this->systemConfigGuestDetails = $systemConfigGuestDetails;
    }

    public function populate(): AbstractEntityForm
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            $this->addField(
                $this->createField(self::FIELD_EMAIL, 'email', [
                    'data' => [
                        'label' => 'Email address',
                        'tooltip' => $this->systemConfigGuestDetails->getEmailAddressTooltip(),
                        'is_required' => true,

                        // Default value will always try to be the quote customer email value.
                        'value' => $quote->getCustomerEmail(),
                    ]
                ])
            );

            $this->modifyField(self::FIELD_EMAIL, function (AbstractEntityField $field) {
                $field->setAttribute('wire:model.defer', $field->getTracePath('data'));
                $field->setAttribute('wire:auto-save', $this->getNamespace());

                if ($this->systemConfigGuestDetails->enableLogin()) {
                    $field->replaceAttribute('wire:auto-save', 'wire:auto-save.self');
                }
            });
        } catch (LocalizedException $exception) {
            $this->logger->critical(
                'An error occurred while attempting to fill out the guest details form.',
                ['exception' => $exception]
            );
        }

        return $this;
    }

    public function getTitle(): string
    {
        return 'Guest Details';
    }
}
