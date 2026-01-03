<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Quote\Model\Cart;

use Exception;
use Hyva\Checkout\Model\Quote\Cart\TotalSegment\ExtensionAttribute\TaxDetailsPool;
use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Model\Cart\TotalsConverter as Subject;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Tax\Model\Config as TaxConfig;
use Psr\Log\LoggerInterface;

class TotalsConverter
{
    private LoggerInterface $logger;
    private TotalSegmentExtensionFactory $totalSegmentExtensionFactory;
    private TaxDetailsPool $taxDetailsPool;
    private TaxConfig $taxConfig;

    public function __construct(
        LoggerInterface $logger,
        TotalSegmentExtensionFactory $totalSegmentExtensionFactory,
        TaxDetailsPool $taxDetailsPool,
        TaxConfig $taxConfig
    ) {
        $this->logger = $logger;
        $this->totalSegmentExtensionFactory = $totalSegmentExtensionFactory;
        $this->taxDetailsPool = $taxDetailsPool;
        $this->taxConfig = $taxConfig;
    }

    /**
     * Adjusts the shipping total value to reflect prices including taxes, if the display setting requires it.
     *
     * @return array<int, array<string, Total>>
     */
    public function beforeProcess(Subject $subject, array $addressTotals): array
    {
        return [$this->fixShippingTotalsValue($addressTotals)];
    }

    /**
     * Injects tax details extension attribute for each total segment if it exists.
     */
    public function afterProcess(Subject $subject, array $totalSegments): array
    {
        foreach ($totalSegments as $name => $segment) {
            try {
                if (! $this->taxDetailsPool->hasTaxDetailsExtensionAttributeItem($name)) {
                    continue;
                }

                $extensionAttributes = $segment->getExtensionAttributes();

                if (! $extensionAttributes) {
                    $extensionAttributes = $this->totalSegmentExtensionFactory->create();
                }

                $extensionAttributes->setTaxDetails([
                    $this->taxDetailsPool->getTaxDetailsExtensionAttributeItem($name)
                ]);

                $segment->setExtensionAttributes($extensionAttributes);
            } catch (Exception $exception) {
                $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $totalSegments;
    }

    private function fixShippingTotalsValue(array $addressTotals): array
    {
        /** @var Total $shippingAddressTotal */
        $shippingAddressTotal = $addressTotals['shipping'] ?? null;

        /*
         * By default, all cart total segments include tax prices when configured, except for shipping, which
         * consistently returns as exclusive of tax. This exception can lead to incorrect display of shipping prices
         * in the total segments, potentially causing developers to recalculate totals to address the issue.
         *
         * However, the shipping total segment item does contain an inclusive tax value. Therefore, we can extract
         * it from there and use it as the inclusive tax value when the cart shipping price needs to be displayed as
         * inclusive of tax or when both inclusive and exclusive tax values should be displayed.
         */
        if ($shippingAddressTotal && ($this->taxConfig->displayCartShippingInclTax() || $this->taxConfig->displayCartShippingBoth())) {
            $value = $shippingAddressTotal->getData('shipping_incl_tax') ?? $shippingAddressTotal->getData('value');
            $shippingAddressTotal->setData('value', $value);
        }

        return $addressTotals;
    }
}
