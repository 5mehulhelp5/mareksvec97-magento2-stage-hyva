<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\PriceSummary;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Design\SystemConfigFormatting;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigFixesWorkarounds;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalSegmentExtensionInterface;
use Magento\Quote\Api\Data\TotalSegmentInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Cart\TotalSegment;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\Config as TaxConfig;

class TotalSegments implements ArgumentInterface
{
    protected CartTotalRepositoryInterface $cartTotalRepository;
    protected SessionCheckout $sessionCheckout;
    protected SerializerInterface $serializer;
    protected ScopeConfigInterface $scopeConfig;

    public function __construct(
        CartTotalRepositoryInterface $cartTotalRepository,
        SessionCheckout $sessionCheckout,
        SerializerInterface $serializer,
        ScopeConfigInterface $scopeConfig,
        private TaxConfig|null $taxConfig = null,
        private SystemConfigFixesWorkarounds|null $fixesWorkaroundsConfig = null,
        private SystemConfigFormatting|null $systemConfigFormatting = null
    ) {
        $this->cartTotalRepository = $cartTotalRepository;
        $this->sessionCheckout = $sessionCheckout;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;

        $this->taxConfig ??= ObjectManager::getInstance()->get(TaxConfig::class);
        $this->fixesWorkaroundsConfig ??= ObjectManager::getInstance()->get(SystemConfigFixesWorkarounds::class);
        $this->systemConfigFormatting ??= ObjectManager::getInstance()->get(SystemConfigFormatting::class);
    }

    /**
     * Retrieves the total amount of the shopping cart, including individual segment totals. Each segment
     * may have extension attributes to accommodate specific rendering requirements or additional information.
     */
    public function getTotals(): ?TotalsInterface
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            if ($this->fixesWorkaroundsConfig->reCollectTotalsInTotalSegments()) {
                $quote->collectTotals();
            }

            $totals = $this->cartTotalRepository->get($quote->getId());
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return null;
        }

        $totalSegmentsData = $totals->getTotalSegments();

        // Remove the 'shipping' segment if no shipping method is selected
        if ($this->systemConfigFormatting->showShippingMethodTotalIfNoneSelected()
            && ! $quote->getShippingAddress()->getShippingMethod()) {
            unset($totalSegmentsData['shipping']);
        }

        $totalsConfigSortOrder = $this->scopeConfig->getValue('sales/totals_sort', ScopeInterface::SCOPE_STORES);

        uasort($totalSegmentsData, static function (TotalSegment $a, TotalSegment $b) use ($totalsConfigSortOrder) {
            return ($totalsConfigSortOrder[$a->getCode()] ?? 0) - ($totalsConfigSortOrder[$b->getCode()] ?? 0);
        });

        $totals->setTotalSegments($totalSegmentsData);
        return $totals;
    }

    /**
     * Try to find a block (by alias) for the given segment.
     *
     * IMPORTANT: Every total needs to have a manually set renderer and can not have a fallback
     * to a default renderer to avoid issues showing total segment items who should not be
     * visible as long as a developer did not provide a renderer block for it.
     *
     * @param array{code: string, title: string, value: float} $segment
     * @return false|AbstractBlock
     */
    public function getTotalBlock(Template $parent, array $segment)
    {
        // The alternative supports backward compatibility with previous version where segments were not found by an block alias.
        $child = $parent->getChildBlock($segment['code']) ?: $parent->getChildBlock('price-summary.total-segment.' . $segment['code']);

        if ($child) {
            $child->setData('segment', $segment);
            $child->setData('tax_config', $this->taxConfig);
        }

        return $child;
    }

    /**
     * Try to find a block (by alias) for the given extension attribute.
     *
     * IMPORTANT: Every extension attribute needs to have a manually set renderer and can not have
     * a fallback to a default renderer to avoid issues showing total segment extension attributes
     * which should not be visible unless a developer provided a renderer block for it.
     *
     * @return false|AbstractBlock
     */
    public function getTotalExtensionAttributesBlock(Template $parent, string $attribute, array $segment)
    {
        $child = $parent->getChildBlock($attribute);

        if ($child) {
            $child->setData('segment', $segment);
            $child->setData('tax_config', $this->taxConfig);
        }

        return $child;
    }

    /**
     * Retrieve an array containing the available extension attributes for the total segment.
     *
     * @return array<string, array<int, TotalSegmentExtensionInterface>>
     */
    public function getTotalSegmentExtensionAttributes(TotalSegmentInterface $totalSegment): array
    {
        $extensionAttributes = $totalSegment->getExtensionAttributes();

        if ($extensionAttributes instanceof AbstractSimpleObject) {
            $data = $extensionAttributes->__toArray();

            // Workaround for scenarios where the attribute returns data directly instead
            // of wrapping it into a single nested array. This alteration is localized to
            // accommodate this specific scenario without causing any disruptions elsewhere.
            if (count($data) !== 1) {
                $nested = $data[$totalSegment->getCode()] ?? null;
                $data = is_array($nested) ? $nested : [$totalSegment->getCode() => $data];
            }

            // Filter out entries that are not arrays. Some extension attributes may be defined
            // as e.g., boolean, string in the XML configuration, but this method
            // should only return array-type extension attributes to maintain type safety.
            return array_filter($data, 'is_array');
        }

        // Ensure that we always return an array in case the extension attributes
        // unexpectedly deviate from being of type AbstractSimpleObject.
        return [];
    }
}
