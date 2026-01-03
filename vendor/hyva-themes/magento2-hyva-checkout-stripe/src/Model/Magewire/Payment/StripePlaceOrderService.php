<?php
/*
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutStripe\Model\Magewire\Payment;

use Exception;
use Hyva\Checkout\Model\Magewire\Payment\AbstractPlaceOrderService;
use Hyva\CheckoutStripe\Exception\AuthenticationRequiredException;
use Hyva\CheckoutStripe\Exception\RequiresActionException;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Message\MessageInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;
use Magewirephp\Magewire\Component;
use StripeIntegration\Payments\Api\ServiceInterface;
use StripeIntegration\Payments\Model\PaymentElement;

class StripePlaceOrderService extends AbstractPlaceOrderService
{
    private Session $checkoutSession;
    private PaymentElement $paymentElement;
    private ServiceInterface $stripeService;
    private string $clientSecret;

    public function __construct(
        CartManagementInterface $cartManagement,
        Session $checkoutSession,
        PaymentElement $paymentElement,
        ServiceInterface $stripeService
    ) {
        parent::__construct($cartManagement);

        $this->checkoutSession = $checkoutSession;
        $this->paymentElement = $paymentElement;
        $this->stripeService = $stripeService;
    }

    /**
     * @throws AuthenticationRequiredException
     * @throws RequiresActionException
     * @throws CouldNotSaveException
     */
    public function placeOrder(Quote $quote): int
    {
        try {
            $result = parent::placeOrder($quote);

            $requiresAction = $this->stripeService->get_requires_action();
            if ($requiresAction !== null) {
                $this->clientSecret = $requiresAction;
                throw new RequiresActionException;
            }

            return $result;
        } catch (Exception $exception) {
            if (strstr($exception->getMessage(), 'Authentication Required: ') !== false) {
                $this->checkoutSession->unsLastRealOrderId();

                throw new AuthenticationRequiredException;
            }

            throw $exception;
        }
    }

    public function canRedirect(): bool
    {
        $paymentIntent = $this->paymentElement->getPaymentIntent();

        if (! $paymentIntent) {
            return false;
        }

        $status = $paymentIntent->status;

        if ($status == 'requires_action' && $paymentIntent->next_action->type == 'redirect_to_url') {
            return true;
        }
        if (in_array($status, ['succeeded', 'requires_capture'])) {
            return true;
        }

        return false;
    }

    public function getRedirectUrl(Quote $quote, ?int $orderId = null): string
    {
        $paymentIntent = $this->paymentElement->getPaymentIntent();

        if (! $paymentIntent || ! $paymentIntent->next_action || $paymentIntent->next_action->type != 'redirect_to_url') {
            return parent::getRedirectUrl($quote, $orderId);
        }

        return $paymentIntent->next_action->redirect_to_url->url;
    }

    public function handleException(Exception $exception, Component $component, Quote $quote): void
    {
        if (! $exception instanceof AuthenticationRequiredException &&
            ! $exception instanceof RequiresActionException
        ) {
            parent::handleException($exception, $component, $quote);

            return;
        }

        $paymentIntent = $this->paymentElement->getPaymentIntent();
        if ($paymentIntent === null) {
            $component->dispatchBrowserEvent(
                'stripe:intent_status:requires_action',
                ['secret' => $this->clientSecret]
            );
            return;
        }

        // Dispatch an event to the browser, so we can hook into it and call handleNextAction
        $component->dispatchBrowserEvent(
            'stripe:intent_status:' . $paymentIntent->status,
            ['secret' => $paymentIntent->client_secret,]
        );

        if ($exception instanceof RequiresActionException) {
            $component->dispatchMessage(
                MessageInterface::TYPE_SUCCESS,
                __('Redirecting, one moment please...')
            );
        }

        if ($exception instanceof AuthenticationRequiredException) {
            $component->dispatchMessage(
                MessageInterface::TYPE_SUCCESS,
                __('You need to enter 3DS secure before continuing.')
            );
        }
    }
}
