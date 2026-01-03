<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\AbstractExpress\Review;

use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Hyva\CheckoutPayPal\Model\CheckoutType;
use Hyva\CheckoutPayPal\Model\Payment\PaypalExpressRedirect;
use Hyva\CheckoutPayPal\Plugin\Paypal\Controller\Express\AbstractExpress\AbstractPayPalExpressPlugin;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Paypal\Controller\Express\AbstractExpress\Review;

class RedirectToCheckout extends AbstractPayPalExpressPlugin
{
    private CheckoutType $checkoutType;
    private RedirectFactory $redirectFactory;
    private PaypalExpressRedirect $paypalExpressRedirect;

    public function __construct(
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        CheckoutType $checkoutType,
        RedirectFactory $redirectFactory,
        PaypalExpressRedirect $paypalExpressRedirect
    ) {
        parent::__construct($hyvaCheckoutConfig);

        $this->checkoutType = $checkoutType;
        $this->redirectFactory = $redirectFactory;
        $this->paypalExpressRedirect = $paypalExpressRedirect;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(Review $review, callable $proceed): ?Redirect
    {
        if ($this->checkoutType->isHyvaCheckout()) {
            return $this->redirectFactory->create()->setPath(
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

        return $proceed();
    }
}
