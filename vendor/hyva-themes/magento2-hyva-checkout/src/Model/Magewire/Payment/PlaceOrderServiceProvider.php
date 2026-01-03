<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Payment;

use Magento\Quote\Api\Data\PaymentInterface;
use Psr\Log\LoggerInterface;

class PlaceOrderServiceProvider
{
    protected PlaceOrderServiceInterface $defaultPlaceOrderService;
    protected LoggerInterface $logger;
    /**
     * @var AbstractPlaceOrderService[]
     */
    protected array $placeOrderServiceList;

    public function __construct(
        PlaceOrderServiceInterface $defaultPlaceOrderService,
        LoggerInterface $logger,
        array $placeOrderServiceList = []
    ) {
        $this->defaultPlaceOrderService = $defaultPlaceOrderService;
        $this->logger = $logger;
        $this->placeOrderServiceList = $placeOrderServiceList;
    }

    /**
     * Returns a Place Order service by PaymentInterface when available.
     *
     * @return AbstractPlaceOrderService
     */
    public function getByPayment(PaymentInterface $payment): ?PlaceOrderServiceInterface
    {
        return $payment->getMethod() ? $this->getByCode($payment->getMethod()) : null;
    }

    /**
     * Returns a Place Order service by payment method code when available.
     *
     * @return AbstractPlaceOrderService
     */
    public function getByCode(string $code): PlaceOrderServiceInterface
    {
        $services = $this->getList();

        if (array_key_exists($code, $services)) {
            return $services[$code];
        }

        foreach ($services as $service) {
            if ($service->canHandle($code)) {
                return $service;
            }
        }

        return $this->getDefaultPlaceOrderService();
    }

    /**
     * @return AbstractPlaceOrderService
     */
    public function getDefaultPlaceOrderService(): PlaceOrderServiceInterface
    {
        return $this->defaultPlaceOrderService;
    }

    /**
     * Get a list of all injected place order services.
     *
     * @return AbstractPlaceOrderService[]
     */
    public function getList(): array
    {
        return $this->placeOrderServiceList;
    }
}
