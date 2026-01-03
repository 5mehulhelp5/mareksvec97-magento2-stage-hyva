<?php

declare(strict_types=1);

namespace Hyva\GeisswebEuvatCheckout\Model\Address;

use Geissweb\Euvat\Helper\Configuration as EuVatConfiguration;
use Geissweb\Euvat\Helper\VatNumber\Formatter;
use Geissweb\Euvat\Api\ValidationRepositoryInterface;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\AbstractEntityFormModifier;
use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Hyva\Checkout\Model\Form\EntityField\EavEntityAddress\CountryAttributeField;

class WithVatIdFormModifier extends AbstractEntityFormModifier
{
    private const CONFIG_PLACEHOLDER_PATH = 'euvat/integration/field_placeholder';
    private const CONFIG_TOOLTIP_PATH = 'euvat/integration/field_tooltip';
    private const CONFIG_OVERWRITE_COMPANY = 'euvat/data_management/overwrite_company_name';
    private const CONFIG_DISABLE_COMPANY = 'euvat/data_management/disable_company_field';
    private const VAT_ID_FIELD_NAME = 'vat_id';

    public function __construct(
        private EuVatConfiguration $configuration,
        private Formatter $formatter,
        private ValidationRepositoryInterface $validationRepository
    ) {}

    public function apply(AbstractEntityForm $form): AbstractEntityForm
    {
        // Add @change event to vat_id field
        $form->registerModificationListener(
            'triggerEventOnVatIdChange',
            'form:build',
            [$this, 'applyTriggerEventOnVatIdChange']
        );

        // Hide vat_id field depending on country
        $form->registerModificationListener(
            'toggleVatIdFieldVisibility',
            'form:build',
            [$this, 'applyToggleVatIdFieldVisibility']
        );

        // Sets form validation rule for vat_id field
        $form->registerModificationListener(
            'setFieldValidation',
            'form:build',
            [$this, 'applySetVatIdFieldValidation']
        );

        // Add Tooltip
        $form->registerModificationListener(
            'addTooltip',
            'form:build',
            [$this, 'applyAddTooltip']
        );

        // Add placeholder attribute
        $form->registerModificationListener(
            'addPlaceholder',
            'form:build',
            [$this, 'applyAddPlaceholder']
        );

        // Cleans VAT number from special chars, adds country id, sets country id if different
        $form->registerModificationListener(
            'vatNumberCleanup',
            'form:' . self::VAT_ID_FIELD_NAME . ':updated',
            [$this, 'applyVatNumberCleanup']
        );

        return $form;
    }

    /**
     * Add placeholder attribute
     */
    public function applyAddPlaceholder(AbstractEntityForm $form): void {
        $vatIdField = $form->getField(self::VAT_ID_FIELD_NAME);
        $placeholder = $this->configuration->getConfig(self::CONFIG_PLACEHOLDER_PATH);
        if (!$vatIdField || empty($placeholder)) {
            return;
        }
        $vatIdField->setAttribute('placeholder', __($placeholder)->render());
    }

    /**
     * Add Tooltip
     */
    public function applyAddTooltip(AbstractEntityForm $form): void {
        $vatIdField = $form->getField(self::VAT_ID_FIELD_NAME);
        $tooltip = $this->configuration->getConfig(self::CONFIG_TOOLTIP_PATH);
        if (!$vatIdField || empty($tooltip)) {
            return;
        }
        $vatIdField->setData('tooltip', __($tooltip)->render());
    }

    /**
     * Cleans VAT number from special chars, adds country id, sets country id if different
     */
    public function applyVatNumberCleanup(AbstractEntityForm $form): void {
        $vatIdField = $form->getField(self::VAT_ID_FIELD_NAME);
        $countryField = $form->getField('country_id');
        if (!$vatIdField || !$vatIdField->getValue() || !$countryField) {
            return;
        }
        $vatNumberCountry = $this->formatter->extractCountryIdFromVatId($vatIdField->getValue());
        if ($this->configuration->getAskCustomerForCountryCorrection()
            && $vatNumberCountry !== ''
            && $this->configuration->isEuCountry($vatNumberCountry)
            && $vatNumberCountry !== $countryField->getValue()
        ) {
            $countryField->setValue($vatNumberCountry);
        }
        $cleanNumber = $this->formatter->formatVatNumber($vatIdField->getValue(), $countryField->getValue());
        $vatIdField->setValue($cleanNumber);

        $this->setCompanyData($form);
    }

    /**
     * Fills in company name from VAT validation (if available)
     * @param \Hyva\Checkout\Model\Form\AbstractEntityForm $form
     *
     * @return void
     */
    protected function setCompanyData(AbstractEntityForm $form): void {
        $companyField = $form->getField('company');
        $vatIdField = $form->getField(self::VAT_ID_FIELD_NAME);
        if (!$companyField || !$vatIdField) {
            return;
        }
        if ((bool)$this->configuration->getConfig(self::CONFIG_OVERWRITE_COMPANY)) {
            $validationData = $this->validationRepository->getByVatId($vatIdField->getValue());
            if (!$validationData) {
                return;
            }
            $traderName = $validationData->getData('vat_trader_name');
            if ($traderName === '' || $traderName === '---') {
                return;
            }
            $companyField->setValue($traderName);

            if ((bool)$this->configuration->getConfig(self::CONFIG_DISABLE_COMPANY)) {
                $companyField->disable();
            }
        }
    }

    /**
     * Sets form validation rule for vat_id field
     */
    public function applySetVatIdFieldValidation(AbstractEntityForm $form): void {
        $vatIdField = $form->getField(self::VAT_ID_FIELD_NAME);
        if (!$vatIdField) {
            return;
        }
        $validations = $this->configuration->getFieldValidationAtCheckout();
        if (!$vatIdField || empty($validations)) {
            return;
        }
        $validation = array_key_first($validations);
        $vatIdField->setValidationRule($validation);
    }

    /**
     * Adds Alpine.js event bindings to the vat_id field for live input and change handling.
     */
    public function applyTriggerEventOnVatIdChange(AbstractEntityForm $form): void
    {
        $vatIdField = $form->getField(self::VAT_ID_FIELD_NAME);
        if (!$vatIdField instanceof EavAttributeField) {
            return;
        }

        $vatIdField->setAttribute('@input', 'handleVatInput');
        $vatIdField->setAttribute('@change.debounce', 'handleVatChange');
    }

    /**
     * Controls the visibility of the vat_id field based on the selected country.
     */
    public function applyToggleVatIdFieldVisibility(AbstractEntityForm $form): void
    {
        $vatIdField = $form->getField(self::VAT_ID_FIELD_NAME);
        $countryField = $form->getField('country_id');

        if (!$vatIdField instanceof EavAttributeField || !$countryField instanceof CountryAttributeField) {
            return;
        }

        $countryCode = (string)$countryField->getValue();
        $namespace = $form->getNamespace();
        $visibleCountries = $this->configuration->getFieldVisibleCountries();
        $isVisible = in_array($countryCode, $visibleCountries, true);

        $vatIdField->setAttribute('data-vat-id-visible', $isVisible ? 'true' : 'false');
        $vatIdField->setAttributeForSection('wrapper', 'x-data', 'initVatIdValidation');
        $vatIdField->setAttributeForSection('wrapper', 'data-address-type', $namespace);
        $vatIdField->setAttributeForSection('wrapper', 'x-show', 'isVisible');
    }
}
