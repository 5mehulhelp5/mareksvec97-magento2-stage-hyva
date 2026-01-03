<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Model\Payment;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Paypal\Model\Api\ProcessableException as ApiProcessableException;
use Magento\Paypal\Model\Express\Checkout as ExpressCheckout;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class PaypalExpressAuthorization
{
    /**
     * Constant does not exist in earlier versions of Magento_Paypal so redefining it here for compatibility.
     *
     * @see ExpressCheckout
     */
    private const PAYMENT_INFO_FUNDING_SOURCE = 'paypal_funding_source';
    
    private CartRepositoryInterface $cartRepository;
    private PaypalExpressConfig $paypalExpressConfig;
    private string $defaultFunding;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        PaypalExpressConfig $paypalExpressConfig,
        string $defaultFunding = 'Paypal'
    ) {
        $this->cartRepository = $cartRepository;
        $this->paypalExpressConfig = $paypalExpressConfig;
        $this->defaultFunding = $defaultFunding;
    }

    public function isAuthorized(CartInterface $quote): bool
    {
        return $this->getPayer($quote) && $this->getToken($quote);
    }

    /**
     * @throws LocalizedException
     */
    public function authorize(CartInterface $quote, string $token, string $payer, string $funding): void
    {
        try {
            $quotePayment = $quote->getPayment();
            $quotePayment->setAdditionalInformation(
                self::PAYMENT_INFO_FUNDING_SOURCE,
                $funding ?: $this->defaultFunding
            );

            $this->paypalExpressConfig->getCheckoutModel($quote)
                ->returnFromPaypal($token, $payer);
        } catch (ApiProcessableException $exception) {
            throw new LocalizedException(__($exception->getUserMessage()));
        } catch (LocalizedException $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        } catch (Exception $exception) {
            throw new LocalizedException(__('We can\'t process Express Checkout approval.'));
        }
    }

    public function cancel(CartInterface $quote): void
    {
        $quotePayment = $quote->getPayment();

        $quotePayment->setAdditionalInformation(self::PAYMENT_INFO_FUNDING_SOURCE, '');
        $quotePayment->setAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_PAYER_ID, '');
        $quotePayment->setAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_TOKEN, '');

        $this->cartRepository->save($quote);
    }

    public function getPayer(CartInterface $quote): string
    {
        $quotePayment = $quote->getPayment();
        return (string) $quotePayment->getAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_PAYER_ID);
    }

    public function getToken(CartInterface $quote): string
    {
        $quotePayment = $quote->getPayment();
        return (string) $quotePayment->getAdditionalInformation(ExpressCheckout::PAYMENT_INFO_TRANSPORT_TOKEN);
    }

    public function getFundingSource(CartInterface $quote): string
    {
        $quotePayment = $quote->getPayment();
        return (string) (
            $quotePayment->getAdditionalInformation(self::PAYMENT_INFO_FUNDING_SOURCE) ?:
                $this->defaultFunding
        );
    }
}
