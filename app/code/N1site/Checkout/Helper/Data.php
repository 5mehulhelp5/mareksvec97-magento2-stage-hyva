<?php
namespace N1site\Checkout\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Quote\Model\Quote;

class Data extends AbstractHelper {
    /**
     * Folder path for shipping logo
     */
    const SHIPPING_LOGO_PATH = 'checkout/shipping_logo';

    /**
     * XML path to free shipping order amount
     */
    const XML_PATH_FREE_SHIPPING_ORDER_AMOUNT = 'sales/freeshipping/amount',
        XML_PATH_POSTCODE_ALLOW_ONLY_DIGITS = 'sales/shipping/postcode_allow_only_digits';

    /**
     * Folder path for payment logo
     */
    const PAYMENT_LOGO_PATH = 'checkout/payment_logo';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Helper constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Retrieve subtotal with discount from quote
     *
     * @param Quote $quote
     * @return float
     */
    public function getSubtotalWithDiscount(Quote $quote, $incTax = true, $minAmount = 0) {
		
        $subtotal = 0;

		// file_put_contents(__DIR__.'/text.txt', '');
		$percent = 0;
        foreach ($quote->getAllItems() as $item) {
			// file_put_contents(__DIR__.'/text.txt', print_r($item->debug(), 1)."\n", FILE_APPEND);
			if ($incTax) {
				$subtotal += $item->getRowTotalInclTax() - $item->getDiscountAmount();
			} else {
				$percent += $item->getTaxPercent();
				$subtotal += $item->getRowTotal() - $item->getDiscountAmount();
				// $subtotal += $minAmount;
				// $subtotal += $item->getRowTotal() * ((100 - floatval($item->getTaxPercent()))/100) - $item->getDiscountAmount();
			}
			// file_put_contents(__DIR__.'/text.txt', 'subtotal: '.$subtotal."\n", FILE_APPEND);
			// file_put_contents(__DIR__.'/text.txt', 'getRowTotal: '.$item->getRowTotal()."\n", FILE_APPEND);
			// file_put_contents(__DIR__.'/text.txt', 'getTaxAmount: '.$item->getTaxAmount()."\n", FILE_APPEND);
			// file_put_contents(__DIR__.'/text.txt', 'getRowTotalInclTax: '.$item->getRowTotalInclTax()."\n\n", FILE_APPEND);
        }
		
		if (count($quote->getAllItems()) && !$incTax) {
			$subtotal -= $minAmount*((100 - $percent/count($quote->getAllItems()))/100) - $minAmount;
		}	

        return $subtotal;
    }

    /**
     * Retrieve carrier logo url by code
     *
     * @param string $carrierCode
     * @return mixed
     */
    public function getCarrierLogoUrl($carrierCode) {
		
        $logo = $this->scopeConfig->getValue("carriers/$carrierCode/logo", ScopeInterface::SCOPE_STORE);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        if (!$logo) {
            return false;
        }

        return $mediaUrl . self::SHIPPING_LOGO_PATH . '/' . $logo;
    }

    /**
     * Retrieve carrier description by code
     *
     * @param string $carrierCode
     * @return mixed
     */
    public function getCarrierDescription($carrierCode) {
        return $this->scopeConfig->getValue("carriers/$carrierCode/description", ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve payment logo url
     *
     * @param string $paymentMethod
     * @return mixed
     */
    public function getPaymentLogoUrl($paymentMethod) {
        $logo = $this->scopeConfig->getValue("payment/$paymentMethod/logo", ScopeInterface::SCOPE_STORE);
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        if (!$logo) {
            return false;
        }

        return $mediaUrl . self::PAYMENT_LOGO_PATH . '/' . $logo;
    }

    /**
     * Retrieve carrier description by code
     *
     * @param string $paymentMethod
     * @return mixed
     */
    public function getPaymentDescription($paymentMethod) {
        return $this->scopeConfig->getValue("payment/$paymentMethod/description", ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve payment config path
     *
     * @param string $methodCode
     * @return string
     */
    public function getPaymentConfigPath($methodCode) {
        return $methodCode;
    }

    /**
     * Retrieve order amount for free shipping
     *
     * @return int
     */
    public function getFreeShippingOrderAmount() {
		if ($this->scopeConfig->getValue('sales/freeshipping/amount', ScopeInterface::SCOPE_WEBSITE)) {
			return (int)$this->scopeConfig->getValue('sales/freeshipping/amount', ScopeInterface::SCOPE_WEBSITE);
		} elseif ($this->scopeConfig->getValue('sales/freeshipping/amount', ScopeInterface::SCOPE_STORE)) {
			return (int)$this->scopeConfig->getValue('sales/freeshipping/amount', ScopeInterface::SCOPE_STORE);
		} elseif ($this->scopeConfig->getValue('amasty_checkout/general/free_shipping_order_amount', ScopeInterface::SCOPE_STORE)) {
			return (int)$this->scopeConfig->getValue('amasty_checkout/general/free_shipping_order_amount', ScopeInterface::SCOPE_STORE);
		}
        return 0;
    }

    /**
     * Check if only digits allowed in postcode
     *
     * @return bool
     */
    public function getPostcodeAllowOnlyDigits() {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_POSTCODE_ALLOW_ONLY_DIGITS, ScopeInterface::SCOPE_STORE);
    }
}