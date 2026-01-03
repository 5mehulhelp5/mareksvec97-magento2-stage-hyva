<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\XReviewBase\Model\Source;

use MageWorx\GeoIP\Helper\Info;

class GeoIpCountry extends \MageWorx\XReviewBase\Model\Source
{
    /**
     * @var Info
     */
    protected $geoIpCountryProvider;

    /**
     * @var array|null
     */
    protected $data;

    /**
     * GeoIpCountry constructor.
     *
     * @param Info $geoIpCountryProvider
     */
    public function __construct(
        Info $geoIpCountryProvider
    ) {
        $this->geoIpCountryProvider = $geoIpCountryProvider;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->data === null) {
            $data = [];

            foreach ($this->geoIpCountryProvider->getMaxmindData() as $countryData) {
                $data[] = [
                    'label' => $countryData['label'],
                    'value' => $countryData['value']
                ];
            }

            $this->data = $data;
        }

        return $this->data;
    }
}
