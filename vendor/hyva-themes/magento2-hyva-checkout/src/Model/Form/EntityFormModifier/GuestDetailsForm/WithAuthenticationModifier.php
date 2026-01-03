<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier\GuestDetailsForm;

use Exception;
use Hyva\Checkout\Magewire\Checkout\GuestDetails;
use Hyva\Checkout\Magewire\Component\AbstractForm;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGuestDetails;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\AbstractEntityFormElement;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityForm\GuestDetailsForm;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magewirephp\Magewire\Exception\AcceptableException;

class WithAuthenticationModifier implements EntityFormModifierInterface
{
    private SessionCustomer $sessionCustomer;
    private CookieManagerInterface $cookieManager;
    private CookieMetadataFactory $cookieMetadataFactory;
    private AccountManagementInterface $accountManagement;
    private SystemConfigGuestDetails $systemConfigGuestDetails;
    private EvaluationResultFactory $evaluationResultFactory;

    public function __construct(
        SessionCustomer $sessionCustomer,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        AccountManagementInterface $accountManagement,
        SystemConfigGuestDetails $systemConfigGuestDetails,
        EvaluationResultFactory $evaluationResultFactory
    ) {
        $this->sessionCustomer = $sessionCustomer;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->accountManagement = $accountManagement;
        $this->systemConfigGuestDetails = $systemConfigGuestDetails;
        $this->evaluationResultFactory = $evaluationResultFactory;
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        if (! $this->systemConfigGuestDetails->enableLogin()) {
            return $form;
        }

        /*
         * Configuring the form to serve as an authentication-specific form,
         * setting up necessary parameters and behaviors for user authentication.
         */
        $form->registerModificationListener(
            'includeAuthentication',
            'form:init',
            fn ($form) => $this->includeAuthentication($form)
        );

        /*
         * Ensures dynamic functionality for the two elements is activated when the form is identified as a
         * Magewire form. This determination is made possible through the use of the "form:build:magewire"
         * modification hook.
         *
         * It's important to note that this hook could alternatively be applied within the apply method.
         * However, registering it here provides assurance that both the password field and the login submit button
         * have been successfully added to the form, making it a more robust implementation.
         */
        $form->registerModificationListener(
            'handleMagewireAuthenticationVisibility',
            'form:build:magewire',
            fn (AbstractEntityForm $form, GuestDetails $component)
                => $this->handleMagewireAuthenticationVisibility($form, $component)
        );

        /*
         * This modification listener is responsible for processing customer authentication logic following the
         * execution of the default form submission action when it yields a positive result. It plays a crucial
         * role in managing the authentication process for the customer.
         */
        $form->registerModificationListener(
            'handleAuthenticationSubmitAction',
            'form:execute:login:magewire',
            fn (AbstractEntityForm $form, GuestDetails $component)
                => $this->handleAuthenticationLoginAction($form, $component)
        );

        return $form;
    }

    private function includeAuthentication(AbstractEntityForm $form)
    {
        $form->modifyField('email_address', function (AbstractEntityField $field) use ($form) {
            $field->setAttribute('autocomplete', 'off');

            $field->setvalidationRule('email');
        });

        $form->addField(
            $form->createField('password', 'password', [
                'data' => [
                    'comment' => 'You already have an account with us. Sign in or continue as guest.',
                    'is_required' => true,
                ]
            ])
                ->setAttribute('autocomplete', 'off')
                ->setAttribute('data-validate-group', 'guest-details')
                ->setAttribute('wire:model.defer', 'data.password')
        );

        $form->addElement(
            $form->createElement('submit', [
                'data' => [
                    'label' => 'Sign In',
                    'layout_alias' => 'login',
                    'method' => 'login'
                ]
            ])
        );
    }

    private function handleMagewireAuthenticationVisibility(AbstractEntityForm $form, GuestDetails $component): AbstractEntityForm
    {
        // Use an extended version of Hyva_Checkout::page/js/api/v1/alpinejs/magewire-form-component.phtml
        $form->setAttribute('x-data', 'initMagewireFormForGuestDetails');
        $form->setAttribute('x-on:submit.prevent', 'login');

        $form->modifyElements(function (AbstractEntityFormElement $element) {
            $element->setAttribute('wire:loading.attr', 'disabled');
        });

        $form->modifyFields(function (AbstractEntityField $field) {
            if ($field->hasAttributesStartingWith('wire:auto-save')) {
                $field->setAttribute('wire:model.defer', $field->getTracePath('data'));
            }
        });

        $customerExists = false;

        if (property_exists($component, 'customerExists')) {
            $customerExists = $component->customerExists;
        }

        // Notify the guest that they can create an account after checkout (similar to the Luma theme).
        $form->modifyField('email_address', function (AbstractEntityField $email) use ($customerExists) {
            if ($customerExists === false) {
                $email->setData('comment', 'You can create an account after checkout.');
            }
        });

        // Hide both the login button and password field simultaneously. Two birds with one stone.
        $form->modifySpecificElements(['submit', 'password'], function (AbstractEntityFormElement $element) use ($customerExists) {
            if ($customerExists === false) {
                $element->hide();
            }
        });

        // Attach a listener to the submit button for the login trigger action, and call the AlpineJS component's login function when clicked.
        $form->modifyElement('submit', function (AbstractEntityFormElement $submit) {
            $submit->setAttribute('x-on:click.prevent', 'login');
        });

        return $form;
    }

    private function handleAuthenticationLoginAction(
        AbstractEntityForm $form,
        AbstractForm $component
    ): AbstractEntityForm {
        try {
            $result = $component->submit($component->getData());

            if (! $result) {
                return $form;
            }
        } catch (AcceptableException $exception) {
            return $form;
        }

        $email = $form->getField('email_address');
        $password = $form->getField('password');

        // Prioritize examining the fundamentals at all times.
        if (! $email || ! $password || ! $password->hasValue()) {
            return $form;
        }

        try {
            $customer = $this->accountManagement->authenticate($email->getValue(), $password->getValue());
            $this->sessionCustomer->setCustomerDataAsLoggedIn($customer);

            if ($this->cookieManager->getCookie('mage-cache-sessid')) {
                $metadata = $this->cookieMetadataFactory->createCookieMetadata();
                $metadata->setPath('/');

                $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
            }

            $component->getEvaluationResultBatch()->push(
                $this->evaluationResultFactory->createRedirect('checkout')->dispatch()
            );
        } catch (LocalizedException $exception) {
            $component->getEvaluationResultBatch()->clear()->push(
                $this->evaluationResultFactory
                    ->createErrorMessageEvent($exception->getMessage(), 'guest:details:error')
                    ->dispatch()
            );
        } catch (Exception $exception) {
            $component->getEvaluationResultBatch()->clear()->push(
                $this->evaluationResultFactory
                    ->createErrorMessageEvent('Invalid login or password.', 'guest:details:error')
                    ->dispatch()
            );
        }

        return $form;
    }
}
