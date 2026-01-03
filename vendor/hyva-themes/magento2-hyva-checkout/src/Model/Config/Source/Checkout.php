<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config\Source;

use Hyva\Checkout\Model\CheckoutInformationProvider;
use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

class Checkout implements OptionSourceInterface
{
    protected HyvaCheckoutConfig $hyvaCheckoutConfig;
    protected CheckoutInformationProvider $checkoutInformationProvider;
    protected LoggerInterface $loggerInterface;

    public function __construct(
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        CheckoutInformationProvider $checkoutInformationProvider,
        LoggerInterface $logger
    ) {
        $this->hyvaCheckoutConfig = $hyvaCheckoutConfig;
        $this->checkoutInformationProvider = $checkoutInformationProvider;
        $this->loggerInterface = $logger;
    }

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        $output = [];

        foreach ($this->checkoutInformationProvider->getList() as $fallback) {
            if ($fallback->canApply()) {
                $output[] = [
                    'value' => $fallback->getNamespace(),
                    'label' => $fallback->getLabel()
                ];
            }
        }

        try {
            foreach ($this->hyvaCheckoutConfig->getList() as $checkout) {
                if (isset($checkout['name']) && $checkout['visible'] ?? true) {
                    $output[] = [
                        'value' => $checkout['name'],
                        'label' => $checkout['label']
                    ];
                }
            }
        } catch (NotFoundException $exception) {
            if (empty($output)) {
                $this->loggerInterface->info('No checkouts nor compatibility checkouts found.');
            }
        }

        return $output;
    }
}
