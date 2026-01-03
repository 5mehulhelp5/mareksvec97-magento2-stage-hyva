<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\Payment\Method;

use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Hyva\Checkout\Model\Magewire\Payment\PlaceOrderServiceProcessor;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface as QuoteRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Magewirephp\Magewire\Component;
use Rakit\Validation\Validator;

class PurchaseOrder extends Component\Form implements EvaluationInterface
{
    public ?string $purchaseOrderNumber = null;

    protected $loader = [
        'purchaseOrderNumber' => 'Saving purchase order number',
    ];

    protected $rules = [
        'purchaseOrderNumber' => 'required'
    ];

    protected $messages = [
        'purchaseOrderNumber:required' => 'The purchase order number is a required field.'
    ];

    protected SessionCheckout $sessionCheckout;
    protected CartRepositoryInterface $quoteRepository;
    protected CartManagementInterface $quoteManagement;
    protected CartRepositoryInterface $cartRepository;
    protected PlaceOrderServiceProcessor $placeOrderServiceProcessor;

    public function __construct(
        Validator $validator,
        SessionCheckout $sessionCheckout,
        CartRepositoryInterface $quoteRepository,
        QuoteRepositoryInterface $cartRepository,
        QuoteManagement $quoteManagement,
        PlaceOrderServiceProcessor $placeOrderServiceProcessor
    ) {
        parent::__construct($validator);

        $this->sessionCheckout = $sessionCheckout;
        $this->quoteRepository = $quoteRepository;
        $this->cartRepository = $cartRepository;
        $this->quoteManagement = $quoteManagement;
        $this->placeOrderServiceProcessor = $placeOrderServiceProcessor;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function mount(): void
    {
        $po = $this->sessionCheckout->getQuote()->getPayment()->getPoNumber();
        $this->purchaseOrderNumber = $po;
    }

    /**
     * Listen for the Purchase Order Number been updated.
     */
    public function updatedPurchaseOrderNumber(string $value): ?string
    {
        $value = empty($value) ? null : $value;

        try {
            $quote = $this->sessionCheckout->getQuote();
            $quote->getPayment()->setPoNumber($value);

            $this->quoteRepository->save($quote);
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage($exception->getMessage());
        }

        return $value;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        if ($this->purchaseOrderNumber === null) {
            return $resultFactory->createErrorMessageEvent()
                ->withCustomEvent('payment:method:error')
                ->withMessage('The purchase order number is a required field.');
        }

        return $resultFactory->createSuccess();
    }
}
