<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\OnAuthorization;

use Hyva\CheckoutPayPal\Model\CheckoutType;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressAuthorization;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressConfig;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Paypal\Controller\Express\OnAuthorization;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Checkout\Model\Session as SessionCheckout;

class Authorize
{
    private UrlInterface $urlBuilder;

    private ResultFactory $resultFactory;

    private SessionCheckout $sessionCheckout;

    private CheckoutType $checkoutType;

    private PaypalExpressAuthorization $paypalExpressAuthorization;

    private PaypalExpressConfig $paypalExpressConfig;

    private MessageManagerInterface $messageManager;

    public function __construct(
        UrlInterface $urlBuilder,
        ResultFactory $resultFactory,
        SessionCheckout $sessionCheckout,
        CheckoutType $checkoutType,
        PaypalExpressAuthorization $paypalExpressAuthorization,
        PaypalExpressConfig $paypalExpressConfig,
        MessageManagerInterface $messageManager
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->resultFactory = $resultFactory;
        $this->sessionCheckout = $sessionCheckout;
        $this->checkoutType = $checkoutType;
        $this->paypalExpressAuthorization = $paypalExpressAuthorization;
        $this->paypalExpressConfig = $paypalExpressConfig;
        $this->messageManager = $messageManager;
    }

    public function aroundExecute(OnAuthorization $onAuthorization, callable $proceed): ResultInterface
    {
        if ($this->checkoutType->isHyvaCheckout()) {
            $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

            $responseContent = $this->authorize($onAuthorization);

            return $controllerResult->setData($responseContent);
        }

        return $proceed();
    }

    private function authorize(OnAuthorization $onAuthorization): array
    {
        $quote = $this->getQuote();

        $payerId = (string) $onAuthorization->getRequest()->getParam('payerId');
        $tokenId = (string) $onAuthorization->getRequest()->getParam('paymentToken');
        $fundingSource = (string) $onAuthorization->getRequest()->getParam('paypalFundingSource');

        try {
            $responseContent = [
                'success' => true,
                'error_message' => '',
            ];

            $this->paypalExpressAuthorization->authorize($quote, $tokenId, $payerId, $fundingSource);

            $responseContent['redirectUrl'] = $this->urlBuilder->getUrl('paypal/express/review');
            $this->sessionCheckout->setQuoteId($quote->getId());
        } catch (LocalizedException $e) {
            $responseContent['success'] = false;
            $responseContent['error_message'] = $e->getMessage();

            $this->messageManager->addErrorMessage($responseContent['error_message']);
        }

        return $responseContent;
    }

    private function getQuote(): CartInterface
    {
        return $this->paypalExpressConfig->getPaypalQuote();
    }
}
