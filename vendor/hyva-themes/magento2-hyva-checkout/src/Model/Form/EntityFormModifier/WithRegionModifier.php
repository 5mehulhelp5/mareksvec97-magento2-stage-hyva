<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Magewire\Checkout\AddressView\AbstractMagewireAddressForm;
use Hyva\Checkout\Model\AvailableRegions;
use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Helper\Data as DirectoryDataHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\AddressInterface;
use Psr\Log\LoggerInterface;

class WithRegionModifier implements EntityFormModifierInterface
{
    protected CountryInformationAcquirerInterface $countryInformationAcquirer;
    protected LoggerInterface $logger;
    protected DirectoryDataHelper $directoryDataHelper;
    protected AvailableRegions $availableRegions;

    public function __construct(
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        DirectoryDataHelper $directoryDataHelper,
        LoggerInterface $logger,
        ?AvailableRegions $availableRegions = null
    ) {
        $this->countryInformationAcquirer = $countryInformationAcquirer;
        $this->directoryDataHelper = $directoryDataHelper;
        $this->logger = $logger;

        $this->availableRegions = $availableRegions
            ?: ObjectManager::getInstance()->get(AvailableRegions::class);
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'applyRegionOptionsByCountryValue',
            'form:build',
            [$this, 'applyRegionOptionsByCountryValue']
        );

        $form->registerModificationListener(
            'resetSelectRegionOnCountryUpdate',
            'form:country_id:updated',
            fn (AbstractEntityForm $form, EntityFieldInterface $field, AbstractMagewireAddressForm $component)
                => $this->resetSelectRegionOnCountryUpdate($form, $field)
        );

        $form->registerModificationListener(
            'applyRegionIndexAsValueOnSelect',
            'form:fill',
            fn (AbstractEntityForm $form, array $values)
                => $this->applyRegionIndexAsValueOnSelect($form, $values)
        );

        return $form;
    }

    public function applyRegionOptionsByCountryValue(AbstractEntityForm $form): AbstractEntityForm
    {
        return $form->modifyField(AddressInterface::KEY_REGION, function (AbstractEntityField $region) use ($form) {
            $countryValue = $this->resolveCountryValue($form->getField(AddressInterface::KEY_COUNTRY_ID));
            $region->setData(EntityFieldInterface::IS_REQUIRED, $this->directoryDataHelper->isRegionRequired($countryValue));

            if (! $this->directoryDataHelper->isShowNonRequiredState() && ! $region->isRequired()) {
                $region->hide();
                return $form;
            }

            $regionOptions = $this->getRegionOptionsByCountry($countryValue);

            if (empty($regionOptions)) {
                $region->clearOptions();
                return $form;
            }

            $region->setOptions(
                array_merge(
                    [[
                        'value' => null,
                        'label' => 'Please select a region, state or province.'
                    ]],
                    array_map(fn ($region) => [
                        'value' => $region->getId(),
                        'label' => $region->getName()
                    ], $regionOptions)
                )
            );

            $region->setData(AbstractEntityField::IS_AUTO_SAVE, true);
            return $form;
        });
    }

    public function resetSelectRegionOnCountryUpdate(AbstractEntityForm $form, EntityFieldInterface $field): AbstractEntityForm
    {
        return $form->modifyField(AddressInterface::KEY_REGION, function (AbstractEntityField $region) use ($form, $field) {
            if ($field->getFrontendInput() === 'select') {
                $region->reset();
            }

            return $form;
        });
    }

    public function applyRegionIndexAsValueOnSelect(AbstractEntityForm $form, array $values): AbstractEntityForm
    {
        return $form->modifyField(AddressInterface::KEY_REGION, function (AbstractEntityField $region) use ($form, $values) {
            $countryValue = $this->resolveCountryValue($form->getField(AddressInterface::KEY_COUNTRY_ID));
            $regionOptions = $this->getRegionOptionsByCountry($countryValue);

            if (! empty($regionOptions) && isset($values['region_id'])) {
                $region->setValue($values['region_id']);
            }

            return $form;
        });
    }

    private function resolveCountryValue(EntityFieldInterface|AbstractEntityField|null $countryField): string
    {
        return $countryField && $countryField->getValue()
            ? (string) $countryField->getValue()
            : $this->directoryDataHelper->getDefaultCountry();
    }

    private function getRegionOptionsByCountry(string $countryValue): array
    {
        try {
            return $this->availableRegions->getAvailableRegions($countryValue) ?? [];
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical(
                'Country info for region select options specification is not available.',
                ['exception' => $exception]
            );

            return [];
        }
    }
}
