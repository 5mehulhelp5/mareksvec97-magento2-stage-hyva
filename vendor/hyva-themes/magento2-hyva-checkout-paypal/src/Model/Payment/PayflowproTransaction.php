<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Payment;

use Magento\Framework\DataObject;
use Magento\Paypal\Model\Payflow\Service\Request\SecureToken;
use Magento\Paypal\Model\Payflowpro;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class PayflowproTransaction
{
    private SecureToken $secureTokenService;

    private CartRepositoryInterface $cartRepository;

    public function __construct(
        SecureToken $secureTokenService,
        CartRepositoryInterface $cartRepository
    ) {
        $this->secureTokenService = $secureTokenService;
        $this->cartRepository = $cartRepository;
    }

    public function requestToken(CartInterface $quote): DataObject
    {
        $token = $this->secureTokenService->requestToken($quote);
        if (!$token->getData('securetoken')) {
            throw new \LogicException();
        }

        return $token;
    }

    public function hasTransaction(CartInterface $quote): bool
    {
        $quotePayment = $quote->getPayment();
        return (bool) $quotePayment->getAdditionalInformation(Payflowpro::PNREF);
    }

    public function resetTransaction(CartInterface $quote): void
    {
        $quotePayment = $quote->getPayment();
        $quotePayment->setAdditionalInformation(Payflowpro::PNREF, '');

        $this->cartRepository->save($quote);
    }
}
