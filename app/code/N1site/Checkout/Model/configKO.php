<?php
namespace N1site\Checkout\Model;

use N1site\Checkout\Helper\Data;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

class configKO extends \Magento\Cms\Block\Block {

	protected $_blockRepository;
	protected $paymentConfig;
	// protected $checkoutHelper;
	protected $layoutFactory;
	protected $helper;
	protected $cart;
	protected $scopeConfig;
	protected $priceCurrency;
    /**
     * Payment Helper Data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;


    function __construct(
		\Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
		\Magento\Payment\Model\Config $paymentConfig,
		// \Magento\Checkout\Helper\Data $checkoutHelper,
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		PriceCurrencyInterface $priceCurrency,
		LayoutFactory $layoutFactory,
        PaymentHelper $paymentHelper,
        Data $helper
	) {
        $this->_blockRepository = $blockRepository;
		$this->paymentConfig = $paymentConfig;
		// $this->checkoutHelper = $checkoutHelper;
		$this->layoutFactory = $layoutFactory;
		$this->helper = $helper;
		$this->cart = $cart;
		$this->scopeConfig = $scopeConfig;
		$this->priceCurrency = $priceCurrency;
        $this->paymentHelper = $paymentHelper;

    }

	public function getConfig() {

		$output = [];

		$paymentMethods = $this->paymentHelper->getPaymentMethods();

		foreach ($paymentMethods as $code=>$method) {
            $paymentCode = $this->helper->getPaymentConfigPath($code);
            $logoUrl = $this->helper->getPaymentLogoUrl($paymentCode);
            $description = $this->helper->getPaymentDescription($paymentCode);
            if ($logoUrl) {
                $output['paymentData'][$code]['logo_url'] = $logoUrl;
            }

            if ($description) {
                $output['paymentData'][$code]['description'] = $description;
            }

			if ( ($code==='cashondelivery') && ($this->scopeConfig->getValue('payment/cashondelivery/enable_payment_fee', ScopeInterface::SCOPE_WEBSITE)) )  {
				$output['paymentData'][$code]['fee'] = ($paymentFee = $this->scopeConfig->getValue('payment/cashondelivery/payment_fee', ScopeInterface::SCOPE_WEBSITE))?$this->formatPrice($paymentFee, true):0;
			}
		}

		return $output;
	}

	private function formatPrice($price, $short = false) {
        return $this->priceCurrency->format(
            $price,
            true,
            ($short)?0:PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->cart->getQuote()->getStore()
        );
	}
}
