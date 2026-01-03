<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\Shipping;

use Hyva\Checkout\Model\Magewire\Component\Evaluation\Batch;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Hyva\Checkout\Exception\CheckoutException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\ShippingMethodManagementInterface;
use Magewirephp\Magewire\Component;
use Psr\Log\LoggerInterface;

class MethodList extends Component implements EvaluationInterface
{
    public ?string $method = null;

    protected $loader = [
        'method' => 'Saving shipping method'
    ];

    protected $listeners = [
        'shipping_address_activated' => 'refresh',
        'shipping_address_saved' => 'refresh',
        'coupon_code_applied' => 'refresh',
        'coupon_code_revoked' => 'refresh'
    ];

    protected SessionCheckout $sessionCheckout;
    protected CartRepositoryInterface $quoteRepository;
    protected ShippingMethodManagementInterface $shippingMethodManagement;
    protected LoggerInterface $logger;
    protected EvaluationResultFactory $evaluationResultFactory;

    private Batch|null $evaluationBatch = null;

    public function __construct(
        SessionCheckout $sessionCheckout,
        CartRepositoryInterface $quoteRepository,
        ShippingMethodManagementInterface $shippingMethodManagement,
        LoggerInterface $logger,
        EvaluationResultFactory|null $evaluationResultFactory = null,
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->quoteRepository = $quoteRepository;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->logger = $logger;
        $this->evaluationResultFactory = $evaluationResultFactory
        ?: ObjectManager::getInstance()->get(EvaluationResultFactory::class);
    }

    public function boot(): void
    {
        try {
            $quote  = $this->sessionCheckout->getQuote();
            $method = $quote->getShippingAddress()->getShippingMethod();
        } catch (LocalizedException $exception) {
            $method = null;
        }

        $this->method = empty($method) ? null : $method;
    }

    public function updatedMethod(string $value): string
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $shippingAddress = $quote->getShippingAddress();

            $rate = $shippingAddress->getShippingRateByCode($value);

            if ($rate === false) {
                throw new CheckoutException(__('Invalid shipping method'));
            }

            $carrier = $rate->getCarrier();
            $method  = $rate->getMethod();

            if ($this->shippingMethodManagement->set($quote->getId(), $carrier, $method)) {
                $this->emit('shipping_method_selected', [
                    'method'  => $method,
                    'carrier' => $carrier,
                    'code'    => sprintf('%s_%s', $carrier, $method),
                ]);
            }
        } catch (CheckoutException $exception) {
            $this->dispatchErrorMessage($exception->getMessage());
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving your shipping preferences.');
        }

        return $value;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        $errorMessageEvent = $resultFactory->createErrorMessageEvent();
        $errorMessageEvent->withCustomEvent('shipping:method:error');

        try {
            $methods = $this->shippingMethodManagement->getList($this->sessionCheckout->getQuoteId());

            if (count($methods) === 0) {
                return $errorMessageEvent->dispatch()->withMessage('No shipping methods available.')->asWarning();
            }
        } catch (NoSuchEntityException | StateException $exception) {
            return $errorMessageEvent->withMessage(
                'Something went wrong while trying to fetch the available shipping methods.'
            );
        }

        if ($this->method === null) {
            return $errorMessageEvent->withMessage(
                'The shipping method is missing. Select the shipping method and try again.'
            );
        }

        $methods = array_filter($methods, function ($method) {
            return $method->getCarrierCode() . '_' . $method->getMethodCode() === $this->method;
        });

        if (count($methods) === 0) {
            return $errorMessageEvent->withMessage(
                'Previous selected shipping method is no longer available. Please select a different shipping method and try again.'
            );
        }

        if ($this->evaluationBatch()->isEmpty()) {
            return $resultFactory->createSuccess([], 'payment:method:success')->dispatch();
        }

        return $this->evaluationBatch();
    }

    protected function evaluationBatch(): Batch
    {
        return $this->evaluationBatch ??= ObjectManager::getInstance()->create(Batch::class);
    }
}
