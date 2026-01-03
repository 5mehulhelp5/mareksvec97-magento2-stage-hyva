<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

class LocationTextCreator
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var \MageWorx\GeoIP\Helper\Country
     */
    protected $geoIpHelper;

    /**
     * @var array
     */
    protected $flagPathCache = [];

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * LocationTextCreator constructor.
     *
     * @param ConfigProvider $configProvider
     * @param \MageWorx\GeoIP\Helper\Country $geoIpHelper
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\ConfigProvider $configProvider,
        \MageWorx\GeoIP\Helper\Country $geoIpHelper,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->configProvider = $configProvider;
        $this->geoIpHelper    = $geoIpHelper;
        $this->escaper        = $escaper;
    }

    /**
     * @param string $countryCode
     * @param string $countryLabel
     * @param string $region
     * @return bool|string|string[]
     */
    public function createText($countryCode, $countryLabel, $region)
    {
        $template = $this->configProvider->getLocationTemplate();

        $html = $template;

        if (strpos($template, '[flag]') !== false) {
            $flagImageHtml = $this->getFlagImageHtml((string)$countryCode, (string)$countryLabel, '');
            $html          = str_replace('[flag]', $flagImageHtml, $html);
        }

        if (strpos($template, '[country]') !== false) {
            $html = str_replace('[country]', __($countryLabel)->getText(), $html);
        }

        if (strpos($template, '[country_code]') !== false) {
            $html = str_replace('[country_code]', $countryCode, $html);
        }

        if (strpos($template, '[state]') !== false) {
            $html = str_replace('[state]', __($region)->getText(), $html);
        }

        return $html;
    }

    /**
     * @param string $countryCode
     * @param string $countryLabel
     * @return string
     */
    public function getFlagImageHtml(string $countryCode, string $countryLabel, string $class): string
    {
        $flagPath = $this->getFlagPath($countryCode);
        $class = $class ? ' class="' . $this->escaper->escapeHtmlAttr($class) . '"' : '';

        return $flagPath ? '<img src="' . $this->escaper->escapeUrl($flagPath) . '" ' .
            'alt="' . $this->escaper->escapeHtmlAttr($countryLabel) . '" ' . $class . '>' : '';
    }

    /**
     * @param string $countryCode
     * @return string|null
     */
    protected function getFlagPath($countryCode): ?string
    {
        if ($countryCode) {
            if (!array_key_exists($countryCode, $this->flagPathCache)) {
                $this->flagPathCache[$countryCode] = $this->geoIpHelper->getFlagPath($countryCode);
            }

            return $this->flagPathCache[$countryCode];
        }

        return null;
    }
}
