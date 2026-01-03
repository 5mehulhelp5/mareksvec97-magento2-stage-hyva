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
use Hyva\Checkout\Magewire\Main;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\NavigationTask;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\PlaceOrderRedirect;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Redirect;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Validation;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\PaymentException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magewirephp\Magewire\Component;
use Psr\Log\LoggerInterface;

/**
 * @internal The Place Order Service is a crucial component within the Place Order Workflow, serving as an integral
 *           part of the framework's internal operations. This method is specifically designed for internal use and
 *           should not be accessed or utilized by custom components outside the designated workflow.
 */
class PlaceOrderServiceProcessor
{
    protected SessionCheckout $sessionCheckout;
    protected LoggerInterface $logger;
    protected PlaceOrderServiceProvider $placeOrderServiceProvider;

    private ?int $orderId = null;
    private EvaluationResultFactory $evaluationResultFactory;

    public function __construct(
        SessionCheckout $sessionCheckout,
        LoggerInterface $logger,
        PlaceOrderServiceProvider $placeOrderServiceProvider,
        ?EvaluationResultFactory $evaluationResultFactory = null
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->logger = $logger;
        $this->placeOrderServiceProvider = $placeOrderServiceProvider;

        $this->evaluationResultFactory = $evaluationResultFactory
            ?: ObjectManager::getInstance()->get(EvaluationResultFactory::class);
    }

    /**
     * @param Main $component
     */
    public function process(Component $component, ?CartInterface $quote = null, array $data = []): self
    {
        /*
         * Note: Due to an oversight, the Main class type for $component was not required in this method.
         * Requiring it now could result in backwards compatibility issues for certain payment implementations.
         * As a temporary measure, we are logging a warning to notify developers, agencies, and merchants that
         * this method has been misused by a payment integration and should be considered for future changes.
         */
        if (! $component instanceof Main) {
            $this->logger->warning(
                'The place order service processor is designated as @internal, indicating that it is ' .
                'intended for internal use only. Third-party extensions should refrain from utilizing this processor ' .
                'for order finalization to ensure proper functionality and compatibility.'
            );
        }

        try {
            $quote = $quote ?? $this->sessionCheckout->getQuote();

            /** @var AbstractPlaceOrderService $placeOrderService */
            $placeOrderService = $this->placeOrderServiceProvider->getByPayment($quote->getPayment());

            if ($placeOrderService === null) {
                throw new PaymentException(
                    __('Unable to process your order due to a missing service. Please consider an alternative payment method.')
                );
            }

            try {
                if ($placeOrderService instanceof AbstractPlaceOrderService) {
                    $placeOrderService->getData()->setData($data);
                }
                if ($placeOrderService->canPlaceOrder()) {
                    $this->orderId = $placeOrderService->placeOrder($quote);
                }
            } catch (Exception $exception) {
                $detail = [
                    'text' => $exception->getMessage(),
                    'method' => $quote->getPayment()->getMethod(),
                ];

                $component->dispatchBrowserEvent('order:place:error', $detail);
                $component->dispatchBrowserEvent(sprintf('order:place:%s:error', $quote->getPayment()->getMethod()), $detail);

                /*
                 * Delegate exception handling to the Place Order Service, allowing for customizable error-handling
                 * strategies. This step may involve rethrowing the exception for further handling or accepting it
                 * gracefully by e.g., logging the details, allowing the method to continue its normal execution.
                 */
                $placeOrderService->handleException($exception, $component, $quote);
            }

            /*
             * Ensuring Backwards Compatibility: Providing temporary support for instances of the
             * place order service that do not adhere to the AbstractPlaceOrderService structure.
             */
            if (! $placeOrderService instanceof AbstractPlaceOrderService || ! $component instanceof Main) {
                return $this->runWithoutEvaluation($component, $placeOrderService, $quote);
            }

            /** @var EvaluationResult $placeOrderEvaluationResult */
            $placeOrderEvaluationResult = $placeOrderService->evaluateCompletion($this->evaluationResultFactory, $this->orderId);
            $component->getEvaluationResultBatch()->push($placeOrderEvaluationResult);

            if ($this->hasSuccess()) {
                $component->getEvaluationResultBatch()->push(
                    $this->evaluationResultFactory->createEvent('order:place:success')->dispatch()
                );
            }

            /*
             * Backwards compatibility for possible redirects towards a PSP platform in order to finalize
             * the payment. Before, this was handled directly by the Magewire redirect feature. This has been
             * bent into handling by evaluation results. Place order services can trigger multiple results.
             */
            if ($placeOrderService->canRedirect() && ! $component->getEvaluationResultBatch()->owns(fn ($value) => $value instanceof Redirect)) {
                $component->getEvaluationResultBatch()->push($this->evaluationResultFactory->create(PlaceOrderRedirect::class, [
                    'url' => $placeOrderService->getRedirectUrl($quote, $this->orderId)
                ]));
            }

            /*
             * As we proceed with the order placement process, it's crucial to ensure that all navigation task
             * and validation evaluation results are marked for execution afterward, guaranteeing their execution.
             */
            $component->getEvaluationResultBatch()->walk(
                fn (NavigationTask $result) => $result->executeAfter(true),
                fn (EvaluationResult $result) => $result instanceof NavigationTask
            )->walk(
                fn (Validation $result) => $result->executeAfter(true),
                fn (EvaluationResult $result) => $result instanceof Validation
            );
        } catch (Exception $exception) {
            $this->handleException($exception, $component);
        }

        return $this;
    }

    public function hasSuccess(): bool
    {
        return $this->orderId !== null;
    }

    private function handleException(Exception $exception, Component $component): void
    {
        $message = 'Something went wrong while processing your order. Please try again.';

        if ($exception instanceof LocalizedException) {
            $message = $exception->getMessage();
        }

        $this->logger->error(
            'Checkout Place Order Exception: ' . $exception->getMessage(),
            ['exception' => $exception]
        );

        /*
         * Temporary facilitation for Non-Main Components: This code includes provisional measures to support
         * components that do not conform to the Main structure. This temporary accommodation is implemented
         * to address transitional scenarios and will be revised as part of ongoing development efforts toward
         * a more standardized architecture.
         */
        if (! $component instanceof Main) {
            $component->dispatchErrorMessage($message);

            return;
        }

        $component->getEvaluationResultBatch()->push(
            $this->evaluationResultFactory->createErrorMessage()->withMessage($message)
        )->dispatch();
    }

    /**
     * This is a temporary workaround to maintain compatibility with place order services
     * that do not extend from AbstractPlaceOrderService. This solution is subject to
     * change and should be replaced with proper implementation in the future.
     */
    private function runWithoutEvaluation(
        Component $component,
        PlaceOrderServiceInterface $placeOrderService,
        Quote $quote
    ): PlaceOrderServiceProcessor {
        if ($placeOrderService->canRedirect()) {
            $component->redirect($placeOrderService->getRedirectUrl($quote, $this->orderId));
        }

        // Fallback insurance: if the order was successfully placed but the Place Order Service did not redirect
        // the customer, this section ensures that the customer gets redirected to the default redirect URL anyway.
        // This is a safety measure to guarantee that the user ends up in the right place after placing an order.
        if ($this->orderId && ! $component->getRedirect()) {
            $component->redirect(
                $this->placeOrderServiceProvider->getDefaultPlaceOrderService()->getRedirectUrl($quote, $this->orderId)
            );
        }

        return $this;
    }
}
