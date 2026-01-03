<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\Controller\Payflow;

use Hyva\CheckoutPayPal\Model\Payment\PayflowIframeProvider;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Session as SessionCheckout;

class Iframe implements HttpGetActionInterface
{
    private SessionCheckout $sessionCheckout;

    private PageFactory $pageFactory;

    private RedirectFactory $redirectFactory;

    private PayflowIframeProvider $payflowIframeProvider;

    public function __construct(
        SessionCheckout $sessionCheckout,
        PageFactory $pageFactory,
        RedirectFactory $redirectFactory,
        PayflowIframeProvider $payflowIframeProvider
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->pageFactory = $pageFactory;
        $this->redirectFactory = $redirectFactory;
        $this->payflowIframeProvider = $payflowIframeProvider;
    }

    public function execute(): ResultInterface
    {
        $order = $this->sessionCheckout->getLastRealOrder();
        if ($this->payflowIframeProvider->isIframeAllowed($order)) {
            return $this->pageFactory->create();
        }

        return $this->redirectFactory->create()->setPath('checkout/onepage/success');
    }
}
