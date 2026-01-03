<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Quote\Cart\TotalSegment\ExtensionAttribute;

use Hyva\Checkout\Api\Data\Quote\TaxDetailsExtensionAttributeInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\TotalsInterface as QuoteTotalsInterface;
use Magento\Quote\Api\Data\TotalsInterfaceFactory;
use Magento\Quote\Model\Cart\Totals;
use Magento\Quote\Model\Quote;

abstract class AbstractTaxDetailsExtensionAttribute implements TaxDetailsExtensionAttributeInterface
{
    private SessionCheckout $sessionCheckout;
    private DataObjectHelper $dataObjectHelper;
    private TotalsInterfaceFactory $totalsFactory;

    private ?Totals $quoteTotals = null;

    public function __construct(
        SessionCheckout $sessionCheckout,
        DataObjectHelper $dataObjectHelper,
        TotalsInterfaceFactory $totalsFactory
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->totalsFactory = $totalsFactory;
    }

    /**
     * @return float
     */
    abstract public function getInclTaxValue(): float;

    /**
     * @return float
     */
    abstract public function getExclTaxValue(): float;

    /**
     * @return bool
     */
    public function showExclTaxPrice(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function showInclTaxPrice(): bool
    {
        return true;
    }

    /**
     * @return \Magento\Quote\Api\Data\TotalsInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuoteTotals(): \Magento\Quote\Api\Data\TotalsInterface
    {
        $quote = $this->sessionCheckout->getQuote();
        $addressTotalsData = $quote->isVirtual()
            ? $quote->getBillingAddress()->getData()
            : $quote->getShippingAddress()->getData();

        unset($addressTotalsData[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        $quoteTotals = $this->totalsFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteTotals,
            $addressTotalsData,
            QuoteTotalsInterface::class
        );

        return $quoteTotals;
    }

    /**
     * @return float
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getTaxRate(): float
    {
        /** @var Quote $quote */
        $quote = $this->sessionCheckout->getQuote();

        foreach ($quote->getTotals() as $segment) {
            if ($segment['code'] === 'tax') {
                $tax = $segment['value'];
                break;
            }
        }

        return $tax ?? 0.0;
    }

    /**
     * @param string $code
     * @return float
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getItemTotalByCode(string $code): float
    {
        if ($this->quoteTotals === null) {
            $this->quoteTotals = $this->getQuoteTotals();
        }

        return (float) $this->quoteTotals[$code] ?? 0.0;
    }
}
