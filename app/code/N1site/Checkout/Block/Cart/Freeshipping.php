<?php
namespace N1site\Checkout\Block\Cart;

class Freeshipping extends \Magento\Checkout\Block\Cart\AbstractCart {
	
    /**
     * @var \N1site\Checkout\Helper\Data
     */
    protected $_checkoutConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \N1site\Checkout\Helper\Data $checkoutConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \N1site\Checkout\Helper\Data $checkoutConfig,
        array $data = []
    ) {
        $this->_checkoutConfig = $checkoutConfig;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
    }

    /**
     * Retrieve order amount for free shipping
     *
     * @return int
     */
    public function getMinOrderAmount() {
        return $this->_checkoutConfig->getFreeShippingOrderAmount();
    }

    /**
     * Retrieve free shipping price
     *
     * @return mixed
     */
    public function getFreeShippingPrice($incTax = true) {
        $quote = $this->getQuote();
		
		// file_put_contents(__DIR__.'/text.txt', print_r($quote->debug(), true)."\n", FILE_APPEND);
		
        $minAmount = $this->getMinOrderAmount();
        $subtotal = $this->_checkoutConfig->getSubtotalWithDiscount($quote, $incTax, $minAmount);

        if ($subtotal >= $minAmount) {

			// $order = $this->getOrder();
			// $quote->setShippingAmount(0);
			// $quote->setBaseShippingAmount(0);
			
			// foreach ($quote->getAllItems() as $item) {
				// $item->setFreeShipping(1);
				// file_put_contents(__DIR__.'/text.txt', print_r($item->debug(), true)."\n", FILE_APPEND);
			// }
			
            return false;
        }
		
		// file_put_contents(__DIR__.'/text.txt', $minAmount."\n", FILE_APPEND);
		// file_put_contents(__DIR__.'/text.txt', $subtotal."\n", FILE_APPEND);

        $freePrice = $minAmount - $subtotal;
        $currentCurrency = $this->_storeManager->getStore()->getCurrentCurrency();

        return $currentCurrency->format($freePrice, [], false);
    }
	
    /**
     * Retrieve tax display
     *
     * @return mixed
     */
    public function getTaxDisplay() {
        return $this->_session->getTaxDisplay();
    }

}
