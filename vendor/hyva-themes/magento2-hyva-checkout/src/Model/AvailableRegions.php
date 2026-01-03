<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AvailableRegions
{
    private CountryInformationAcquirerInterface $countryInformationAcquirer;

    private array $memoizedRegions = [];

    public function __construct(
        CountryInformationAcquirerInterface $countryInformationAcquirer
    ) {
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getAvailableRegions(string $country)
    {
        if (! isset($this->memoizedRegions[$country])) {
            $countryInfo = $this->countryInformationAcquirer->getCountryInfo($country);
            $this->memoizedRegions[$country] = $countryInfo->getAvailableRegions();
        }

        return $this->memoizedRegions[$country];
    }
}
