<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\ErrorMessageConfigInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Magento\Store\Model\ScopeInterface;

class ValidateReCaptchaCommand
{
    private ScopeConfigInterface $scopeConfig;

    private ValidationConfigResolverInterface $validationConfigResolver;

    private ValidatorInterface $captchaValidator;

    private ErrorMessageConfigInterface $errorMessageConfig;

    private ValidationErrorMessagesProvider $validationErrorMessagesProvider;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        ErrorMessageConfigInterface $errorMessageConfig,
        ValidationErrorMessagesProvider $validationErrorMessagesProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->errorMessageConfig = $errorMessageConfig;
        $this->validationErrorMessagesProvider = $validationErrorMessagesProvider;
    }

    /**
     * @throws LocalizedException|InputException
     */
    public function execute(array $requestData, string $formId = 'paypal_payflowpro'): void
    {
        if (!$this->isEnabled($formId)) {
            return;
        }

        $reCaptchaResponse = $requestData[CaptchaResponseResolverInterface::PARAM_RECAPTCHA] ?? '';
        if (empty($reCaptchaResponse)) {
            throw new InputException(__('Can not resolve reCAPTCHA parameter.'));
        }

        $validationConfig = $this->validationConfigResolver->get($formId);

        $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
        if (false === $validationResult->isValid()) {
            $this->processError($validationResult->getErrors());
        }
    }

    private function isEnabled(string $formId): bool
    {
        return $this->scopeConfig->isSetFlag('recaptcha_frontend/type_for/' . $formId, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @throws LocalizedException
     */
    private function processError(array $errorMessages): void
    {
        $validationErrorText = $this->errorMessageConfig->getValidationFailureMessage();
        $technicalErrorText = $this->errorMessageConfig->getTechnicalFailureMessage();

        $message = $errorMessages ? $validationErrorText : $technicalErrorText;

        foreach ($errorMessages as $errorMessageCode => $errorMessageText) {
            if (!$this->isValidationError($errorMessageCode)) {
                $message = $technicalErrorText;
            }
        }

        throw new LocalizedException(__($message));
    }

    private function isValidationError(string $errorMessageCode): bool
    {
        return $errorMessageCode !== $this->validationErrorMessagesProvider->getErrorMessage($errorMessageCode);
    }
}
