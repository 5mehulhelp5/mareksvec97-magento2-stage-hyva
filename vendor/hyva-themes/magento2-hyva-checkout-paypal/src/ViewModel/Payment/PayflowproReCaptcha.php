<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\ViewModel\Payment;

use Hyva\Theme\ViewModel\ReCaptcha;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class PayflowproReCaptcha implements ArgumentInterface
{
    public const FORM_ID = ReCaptcha::RECAPTCHA_FORM_ID_PAYPAL_PAYFLOWPRO;

    private const XML_PATH_RECAPTCHA_SITE_KEY = 'recaptcha_frontend/type_%s/public_key';

    private ReCaptcha $reCaptcha;

    private ScopeConfigInterface $scopeConfig;

    private ?bool $isEnabled = null;

    private ?array $reCaptchaData = null;

    public function __construct(
        ReCaptcha $reCaptcha,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->reCaptcha = $reCaptcha;
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        if ($this->isEnabled !== null) {
            return $this->isEnabled;
        }

        $this->isEnabled = (bool) $this->getReCaptchaData();

        return $this->isEnabled;
    }

    public function getReCaptchaData(): array
    {
        if ($this->reCaptchaData !== null) {
            return $this->reCaptchaData;
        }

        $this->reCaptchaData = $this->reCaptcha->getRecaptchaData(self::FORM_ID) ?? [];

        return $this->reCaptchaData;
    }

    public function getReCaptchaInputHtml(): string
    {
        return $this->reCaptcha->getInputHtml(self::FORM_ID);
    }

    public function getReCaptchaNoticeHtml(): string
    {
        return $this->reCaptcha->getLegalNoticeHtml(self::FORM_ID);
    }

    public function getReCaptchaValidationJsHtml(): string
    {
        return $this->reCaptcha->getValidationJsHtml(self::FORM_ID);
    }

    public function getReCaptchaVersion(): string
    {
        return (string) $this->scopeConfig->getValue(
            ReCaptcha::XML_CONFIG_PATH_RECAPTCHA . static::FORM_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isV2NotRobotReCaptcha(): bool
    {
        return $this->getReCaptchaVersion() === 'recaptcha';
    }

    public function isV2InvisibleReCaptcha(): bool
    {
        return $this->getReCaptchaVersion() === 'invisible';
    }

    public function isV3InvisibleReCaptcha(): bool
    {
        return $this->getReCaptchaVersion() === 'recaptcha_v3';
    }

    public function getContainerId(): string
    {
        return 'grecaptcha-container-' . $this->reCaptcha->calcJsInstanceSuffix(self::FORM_ID);
    }

    public function getV2InvisibleCallback(): string
    {
        return 'googleRecaptchaCallback' . $this->reCaptcha->calcJsInstanceSuffix(self::FORM_ID);
    }

    public function getSiteKey(): string
    {
        return (string) $this->scopeConfig->getValue(
            sprintf(self::XML_PATH_RECAPTCHA_SITE_KEY, $this->getReCaptchaVersion()),
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
