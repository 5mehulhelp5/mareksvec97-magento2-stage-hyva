<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout;

use Hyva\Checkout\Magewire\Component\AbstractForm;
use Hyva\Checkout\Model\Form\EntityForm\GuestDetailsForm;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Batch as EvaluationBatchResult;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magewirephp\Magewire\Exception\AcceptableException;
use Psr\Log\LoggerInterface;
use Rakit\Validation\Validator;

class GuestDetails extends AbstractForm
{
    public bool $customerExists = false;

    private AccountManagementInterface $accountManagement;

    public function __construct(
        Validator $validator,
        GuestDetailsForm $form,
        AccountManagementInterface $accountManagement,
        LoggerInterface $logger,
        EvaluationBatchResult $evaluationBatch
    ) {
        parent::__construct($validator, $form, $logger, $evaluationBatch);

        $this->accountManagement = $accountManagement;
    }

    public function boot(): void
    {
        parent::boot();

        $email = $this->getForm()->getField(GuestDetailsForm::FIELD_EMAIL);

        /*
         * Customer existence can only be verified in cases where the customer has previously entered
         * an email address, then either reloaded the page or returned to the step where the email field is located.
         *
         * Therefore, during initialization, a value will only be present in this
         * field if it was saved from a prior interaction.
         */
        if ($email && $email->hasValue()) {
            $this->handleCustomerExistence($email->getValue());
        }
    }

    /**
     * Magewire-specific magic method that listens for changes to `data.email_address`.
     *
     * @throws AcceptableException
     */
    public function updatedDataEmailAddress($value)
    {
        $this->handleCustomerExistence($value);

        $this->submit([GuestDetailsForm::FIELD_EMAIL => $value]);

        return $value;
    }

    /**
     * Set the `customerExists` property to true or false based on whether the provided email address is valid
     * and available, as verified through Magento's core account management.
     */
    private function handleCustomerExistence(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $this->customerExists = ! $this->accountManagement->isEmailAvailable($email);
            } catch (LocalizedException $exception) {
                $this->logger->critical(
                    sprintf('Something went wrong while checking the availability of the email address %s', $email),
                    ['exception' => $exception]
                );
            }
        }
    }
}
