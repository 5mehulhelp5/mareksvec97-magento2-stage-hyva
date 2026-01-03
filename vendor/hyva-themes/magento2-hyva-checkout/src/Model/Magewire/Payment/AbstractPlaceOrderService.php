<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Payment;

use Exception;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;
use Magewirephp\Magewire\Component;

abstract class AbstractPlaceOrderService implements PlaceOrderServiceInterface, EvaluationInterface
{
    public const REDIRECT_PATH = 'checkout/onepage/success';

    protected CartManagementInterface $cartManagement;
    protected AbstractOrderData $orderData;

    public function __construct(
        CartManagementInterface $cartManagement,
        ?AbstractOrderData $orderData = null
    ) {
        $this->cartManagement = $cartManagement;

        $this->orderData = $orderData
            ?: ObjectManager::getInstance()->create(DefaultOrderData::class);
    }

    /**
     * @throws CouldNotSaveException
     */
    public function placeOrder(Quote $quote): int
    {
        // Typecast is required since default Magento does return a numeric value, but not of type int.
        return (int) $this->cartManagement->placeOrder($quote->getId(), $quote->getPayment());
    }

    public function canPlaceOrder(): bool
    {
        return true;
    }

    /**
     * @throws Exception
     */
    public function handleException(Exception $exception, Component $component, Quote $quote): void
    {
        throw $exception;
    }

    public function canRedirect(): bool
    {
        return true;
    }

    public function getRedirectUrl(Quote $quote, ?int $orderId = null): string
    {
        $orderPlaceRedirectUrl = (string)($quote->getPayment()->getOrderPlaceRedirectUrl() ?? '');
        if ($orderPlaceRedirectUrl !== '') {
            return $orderPlaceRedirectUrl;
        }

        return self::REDIRECT_PATH;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory, int|null $orderId = null): EvaluationResultInterface
    {
        return $resultFactory->createSuccess();
    }

    public function getData()
    {
        return $this->orderData;
    }

    public function canHandle(string $code): bool
    {
        return false;
    }
}
