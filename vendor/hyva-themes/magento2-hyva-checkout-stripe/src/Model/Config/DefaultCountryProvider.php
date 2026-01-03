<?php
declare(strict_types=1);

namespace Hyva\CheckoutStripe\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class DefaultCountryProvider
{
    private const XML_PATH_DEFAULT_COUNTRY = 'general/country/default';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * Returns the store's default country (e.g., "US", "DE", "NL").
     */
    public function getDefaultCountry(?int $storeId = null): string
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return trim($value);
    }
}
