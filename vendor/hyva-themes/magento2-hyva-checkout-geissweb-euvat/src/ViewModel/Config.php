<?php

declare(strict_types=1);

namespace Hyva\GeisswebEuvatCheckout\ViewModel;

use Geissweb\Euvat\Helper\Configuration;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Exposes EUVAT configuration flags to templates and can build a CSP-safe front-end config payload.
 */
class Config implements ArgumentInterface
{
    public function __construct(
        private readonly Configuration $config,
        private readonly Json $json
    ) {}

    /**
     * Whether a different country prefix in the VAT number is allowed (vs. selected country).
     */
    public function allowDifferentVatNumberPrefix(): bool
    {
        return (bool) $this->config->allowDifferentVatNumberPrefix();
    }

    /**
     * Merchant (store) country code (e.g., "DE").
     */
    public function getMerchantCountryCode(): string
    {
        return (string) $this->config->getMerchantCountryCode();
    }
    
    public function isDebugEnabled(): bool
    {
        return (bool) $this->config->getConfig('euvat/mod_info/debug');
    }

    /**
     * Build a ready-to-embed config array for the front end (to JSON-encode into data-vat-config).
     *
     * @param string                   $addressType    e.g. "shipping" or "billing"
     * @param string                   $vatNumber      current VAT number (optional)
     * @param string                   $countryId      current country (optional)
     * @param int                      $autoSaveTime   debounce in ms (optional)
     * @param array<string,mixed>|string|null $initialResult Optional initial validation result
     *                                                      (as assoc array OR JSON string)
     *                                                      with keys: vat_is_valid, vat_request_success, request_message
     *
     * @return array<string,mixed>
     */
    public function buildVatConfig(
        string $addressType,
        string $vatNumber = '',
        string $countryId = '',
        int $autoSaveTime = 500,
        array|string|null $initialResult = null
    ): array {
        $normalizedInitial = null;

        if (is_string($initialResult) && $initialResult !== '') {
            try {
                /** @var array<string,mixed>|null $decoded */
                $decoded = $this->json->unserialize($initialResult);
                if (is_array($decoded)) {
                    $normalizedInitial = $decoded;
                }
            } catch (\Throwable) {
                // ignore invalid JSON; leave $normalizedInitial = null
            }
        } elseif (is_array($initialResult)) {
            $normalizedInitial = $initialResult;
        }

        if ($normalizedInitial !== null) {
            // Defensive normalization
            $normalizedInitial = [
                'vat_is_valid'        => (bool) ($normalizedInitial['vat_is_valid'] ?? false),
                'vat_request_success' => (bool) ($normalizedInitial['vat_request_success'] ?? false),
                'request_message'     => (string) ($normalizedInitial['request_message'] ?? ''),
            ];
        }

        $payload = [
            'addressType'          => $addressType,
            'vatNumber'            => $vatNumber,
            'countryId'            => $countryId,
            'autoSaveTime'         => $autoSaveTime,
            'allowDifferentPrefix' => $this->allowDifferentVatNumberPrefix(),
            'merchantCountry'      => $this->getMerchantCountryCode(),
        ];

        if ($normalizedInitial !== null) {
            $payload['initialResult'] = $normalizedInitial;
        }

        return $payload;
    }
}
