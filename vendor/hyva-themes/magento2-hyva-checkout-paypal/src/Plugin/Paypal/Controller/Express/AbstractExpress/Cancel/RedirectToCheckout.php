<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\AbstractExpress\Cancel;

use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Hyva\CheckoutPayPal\Model\CheckoutType;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressRedirect;
use Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\AbstractExpress\AbstractPayPalExpressPlugin;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Paypal\Controller\Express\AbstractExpress\Cancel;

class RedirectToCheckout extends AbstractPayPalExpressPlugin
{
    private CheckoutType $checkoutType;
    private PaypalExpressRedirect $paypalExpressRedirect;

    public function __construct(
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        CheckoutType $checkoutType,
        PaypalExpressRedirect $paypalExpressRedirect
    ) {
        parent::__construct($hyvaCheckoutConfig);

        $this->checkoutType = $checkoutType;
        $this->paypalExpressRedirect = $paypalExpressRedirect;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(Cancel $cancel, Redirect $resultRedirect): Redirect
    {
        if ($this->checkoutType->isHyvaCheckout()) {
            $resultRedirect->setPath(
                'checkout',
                [
                    '_query' => [
                        'step' => $this->isMultiStepCheckout()
                            ? $this->paypalExpressRedirect->getRedirectStep()
                            : null,
                    ]
                ]
            );
        }

        return $resultRedirect;
    }
}
