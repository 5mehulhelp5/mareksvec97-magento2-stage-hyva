<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\ServiceInputProcessor;

class CheckoutFactory
{
    private ServiceInputProcessor $serviceInputProcessor;
    private Config $config;

    public function __construct(
        ServiceInputProcessor $serviceInputProcessor,
        Config $config
    ) {
        $this->serviceInputProcessor = $serviceInputProcessor;
        $this->config = $config;
    }

    /**
     * @throws LocalizedException
     */
    public function create(?string $checkout = null, ?array $data = null): Checkout
    {
        $data ??= $this->config->getDataByPath([
            $checkout ?? $this->config->getActiveCheckoutNamespace()
        ]);

        if ($checkout === null) {
            $data ??= $this->config->getDataByPath([
                $this->config->getActiveCheckoutNamespace()
            ]);
        }

        $checkout = $this->serviceInputProcessor->convertValue($data, Checkout::class);
        return $checkout->refreshAvailableSteps();
    }
}
