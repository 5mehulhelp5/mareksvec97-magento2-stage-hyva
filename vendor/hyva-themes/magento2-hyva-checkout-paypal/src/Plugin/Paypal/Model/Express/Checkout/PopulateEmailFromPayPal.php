<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Plugin\Paypal\Model\Express\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Paypal\Model\Express\Checkout;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

class PopulateEmailFromPayPal
{
    private CheckoutSession $checkoutSession;
    private CartRepositoryInterface $quoteRepository;
    private LoggerInterface $logger;

    public function __construct(
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * Ensure the customer email address returned from PayPal also exists in the quote.
     *
     * @param Checkout $subject
     * @param null $result
     * @return null
     */
    public function afterReturnFromPaypal(Checkout $subject, $result)
    {
        try {
            $quote = $this->checkoutSession->getQuote();

            if (!$quote->getCustomerIsGuest()) {
                return null; // Magento uses the customer's email address, not the one returned from PayPal
            }

            $shippingAddress = $quote->getShippingAddress();

            if (!$quote->getCustomerEmail() && $shippingAddress && $shippingAddress->getEmail()) {
                $quote->setCustomerEmail($shippingAddress->getEmail());
                $this->quoteRepository->save($quote);
            }
        } catch (CouldNotSaveException | InputException | LocalizedException | NoSuchEntityException $e) {
            $this->logger->error(sprintf(
                'Could not copy email address to quote after receiving PayPal Express response: %s',
                $e->getMessage()
            ));
        }

        return null;
    }
}
