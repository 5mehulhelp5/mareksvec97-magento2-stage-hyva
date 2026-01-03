<?php

namespace N1site\Checkout\Model;

class CartUpdate extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {
	
	protected $priceCurrency;
	protected $scopeConfig;

	public function __construct(
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		$this->priceCurrency = $priceCurrency;
		$this->scopeConfig = $scopeConfig;
	}

	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	) {
		parent::collect($quote, $shippingAssignment, $total);
		
		$total->setBaseGrandTotal(false);
		
		return $this;
		

		$discountSubtotal = $this->scopeConfig->getValue('sales/custom_discount/discount_subtotal', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if (!$discountSubtotal) return $this;
		
		$discountAmount = $_discountAmount = $this->scopeConfig->getValue('sales/custom_discount/discount_amount', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if (!$discountAmount) return $this;
		
		$subtotal = $quote->getSubtotal();
		
		if ($subtotal<$discountSubtotal) {
			$discountAmount = ($subtotal*$discountAmount)/$discountSubtotal;
		}
		
		$customDiscount = ($subtotal*$discountAmount)/100;
		// if ($customDiscount>$_discountAmount) $customDiscount = $_discountAmount;
		
		$discount = $this->priceCurrency->convert($customDiscount);
		
		$total->setTotalAmount('discount', -$discount);
		$total->setBaseTotalAmount('discount', -$customDiscount);
		$total->setBaseGrandTotal($total->getBaseGrandTotal() - $customDiscount);
		$total->setDiscountDescription(str_replace('.', ',', round($discountAmount, 1)).' %');
		
		$quote->setDiscount(-$discount);
		
		return $this;
	}
	
}