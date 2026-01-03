<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\Shipping;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigDesign;
use Hyva\Checkout\Model\ShippingMethodMetaData;
use Hyva\Checkout\Model\ShippingMethodMetaDataFactory;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Tax\Model\Config as TaxConfig;

class MethodList implements ArgumentInterface
{
    protected SessionCheckout $sessionCheckout;
    protected ShippingMethodManagementInterface $shippingMethodManagement;

    public function __construct(
        SessionCheckout $sessionCheckout,
        ShippingMethodManagementInterface $shippingMethodManagement,
        protected TaxConfig|null $taxConfig = null,
        protected ShippingMethodMetaDataFactory|null $shippingMethodMetaDataFactory = null,
        protected SystemConfigDesign|null $designConfig = null
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->shippingMethodManagement = $shippingMethodManagement;

        $this->taxConfig ??= ObjectManager::getInstance()->get(TaxConfig::class);
        $this->shippingMethodMetaDataFactory ??= ObjectManager::getInstance()->get(ShippingMethodMetaDataFactory::class);
        $this->designConfig ??= ObjectManager::getInstance()->get(SystemConfigDesign::class);
    }

    /**
     * Get all available shipping methods.
     */
    public function getList(): ?array
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            return $this->shippingMethodManagement->estimateByExtendedAddress($quote->getId(), $quote->getShippingAddress());
        } catch (LocalizedException $exception) {
            return null;
        }
    }

    /**
     * Try to get the shipping method's additional child block.
     *
     * @return false|AbstractBlock
     */
    public function getAdditionalViewBlock(Template $block, ShippingMethodInterface $method)
    {
        return $block->getChildBlock($method->getCarrierCode() . '_' . $method->getMethodCode());
    }

    /**
     * Validate if the given shipping method has an additional child block.
     */
    public function hasAdditionalView(Template $block, ShippingMethodInterface $method): bool
    {
        $search = $this->getAdditionalViewBlock($block, $method);
        return $search && $search->getTemplate();
    }

    /**
     * Validate if the given target is the same as the given shipping method.
     */
    public function isCurrentShippingMethod(ShippingMethodInterface $method, ?string $target): bool
    {
        return $target && $method->getCarrierCode() . '_' . $method->getMethodCode() === $target;
    }

    /**
     * Return the price incl. or excl. tax, depending on the system setting.
     */
    public function getMethodPrice(ShippingMethodInterface $method): float
    {
        return (float) $this->taxConfig->displayCartShippingExclTax()
            ? $method->getPriceExclTax()
            : $method->getPriceInclTax();
    }

    /**
     * Try to return the method's layout block.
     *
     * @return false|AbstractBlock
     */
    public function getMethodBlock(Template $block, ShippingMethodInterface $method)
    {
        $child = $block->getChildBlock($method->getCarrierCode() . '_' . $method->getMethodCode());
        return $child ? $child->setData('method', $method) : false;
    }

    /**
     * Returns a method meta-data object including data set via the given block argument named by the shipping method
     * code.
     */
    public function getMethodMetaData(Template $parent, ShippingMethodInterface $method): ShippingMethodMetaData
    {
        $block = $this->getMethodBlock($parent, $method);
        $arguments = $block ? $block->getData('metadata') : [];

        return $this->shippingMethodMetaDataFactory->create(['data' => $arguments ?? [], 'method' => $method]);
    }

    /**
     * Get the display title for a shipping method.
     *
     * Returns either the method title alone or combined with the carrier title
     * based on the design configuration setting.
     *
     * @param ShippingMethodInterface $method
     * @return string
     */
    public function getMethodTitle(ShippingMethodInterface $method): string
    {
        $methodTitle = $method->getMethodTitle();

        if (!$this->designConfig->canCombineShippingMethodName()) {
            return $methodTitle;
        }

        $carrierTitle = $method->getCarrierTitle();

        if (empty($carrierTitle)) {
            return $methodTitle;
        }

        return $methodTitle . ' - ' . $carrierTitle;
    }
}
