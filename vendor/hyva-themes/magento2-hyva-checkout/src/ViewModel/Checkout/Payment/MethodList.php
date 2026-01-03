<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout\Payment;

use Exception;
use Hyva\Checkout\Model\MethodMetaDataFactory;
use Hyva\Checkout\Model\MethodMetaDataInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Payment\Model\MethodInterface as PaymentMethodInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Psr\Log\LoggerInterface;

class MethodList implements ArgumentInterface
{
    protected CheckoutSession $checkoutSession;
    protected PaymentMethodManagementInterface $paymentMethodManagement;
    protected LoggerInterface $logger;
    protected MethodMetaDataFactory $methodMetaDataFactory;

    public function __construct(
        PaymentMethodManagementInterface $paymentMethodManagement,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger,
        MethodMetaDataFactory $methodMetaDataFactory
    ) {
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->methodMetaDataFactory = $methodMetaDataFactory;
    }

    /**
     * Get all available payment methods.
     */
    public function getList(): ?array
    {
        try {
            return $this->paymentMethodManagement->getList($this->checkoutSession->getQuote()->getId());
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Try to return the method's layout block.
     *
     * @return false|AbstractBlock
     */
    public function getMethodBlock(Template $block, PaymentMethodInterface $method)
    {
        $child = $block->getChildBlock($method->getCode());
        return $child ? $child->setData('method', $method) : false;
    }

    /**
     * Can show the requested payment method.
     */
    public function canShowMethod(Template $block, PaymentMethodInterface $target, ?string $current = null): bool
    {
        $search = $this->getMethodBlock($block, $target);

        return $search
            && $search->getTemplate()
            && $current
            && $target->getCode() === $current;
    }

    /**
     * Returns a method meta-data object including data set via the given
     * block argument named by the payment method code.
     */
    public function getMethodMetaData(Template $parent, PaymentMethodInterface $method): MethodMetaDataInterface
    {
        $block = $this->getMethodBlock($parent, $method);
        $arguments = $block ? $block->getData('metadata') : [];

        return $this->methodMetaDataFactory->create(['data' => $arguments ?? [], 'method' => $method]);
    }
}
