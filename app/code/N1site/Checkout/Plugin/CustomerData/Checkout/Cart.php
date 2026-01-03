<?php

namespace N1site\Checkout\Plugin\CustomerData\Checkout;

use Magento\Framework\View\LayoutFactory;

class Cart {
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutHelper;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * CartPlugin constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        LayoutFactory $layoutFactory
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param $result
     * @return array
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, array $result)
    {
        $totals = $this->getQuote()->getTotals();
        $result['grand_total'] = $this->checkoutHelper->formatPrice($totals['grand_total']->getValue());
        // $result['freeshipping_info'] = $this->getFreeshippingBlock();

        return $result;
    }

    /**
     * Retrieve free shipping block content
     *
     * @return string
     */
    protected function getFreeshippingBlock()
    {
        return $this->layoutFactory->create()
            ->createBlock('N1site\Checkout\Block\Cart\Freeshipping')
            ->setTemplate('cart/freeshipping.phtml')
            ->toHtml();
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }
}