<?php

namespace N1site\Checkout\Plugin\Helper;

// use Maghos\Gopay\Model\Payment\Bank;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use N1site\Checkout\Helper\Data as CheckoutHelper;

class Data
{
    /**
     * XML config path for Gopay bank
     */
    const XML_PATH_CONFIG_BANK_LOGO = 'payment/maghos_gopay/bank_logo',
        XML_PATH_CONFIG_BANK_DESCRIPTION = 'payment/maghos_gopay/bank_description';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Plugin constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve carrier description by code
     *
     * @param CheckoutHelper $subject
     * @param \Closure $proceed
     * @param string $paymentMethod
     * @return string
     */
    public function aroundGetPaymentLogoUrl(
        CheckoutHelper $subject,
        \Closure $proceed,
        $paymentMethod
    ) {
        // if ($paymentMethod !== Bank::CODE) {
            return $proceed($paymentMethod);
        // }

        // $logo = $this->scopeConfig->getValue(self::XML_PATH_CONFIG_BANK_LOGO, ScopeInterface::SCOPE_STORE);
        // $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        // if (!$logo) {
            // return false;
        // }

        // return $mediaUrl . $subject::PAYMENT_LOGO_PATH . '/' . $logo;
    }

    /**
     * Retrieve carrier description by code
     *
     * @param CheckoutHelper $subject
     * @param \Closure $proceed
     * @param string $paymentMethod
     * @return string
     */
    public function aroundGetPaymentDescription(
        CheckoutHelper $subject,
        \Closure $proceed,
        $paymentMethod
    ) {
        // if ($paymentMethod !== Bank::CODE) {
            return $proceed($paymentMethod);
        // }

        // return $this->scopeConfig->getValue(self::XML_PATH_CONFIG_BANK_DESCRIPTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve payment config path
     *
     * @param CheckoutHelper $subject
     * @param array $result
     * @param string $methodCode
     * @return array
     */
    public function afterGetPaymentConfigPath(
        CheckoutHelper $subject,
        $result,
        $methodCode
    ) {
        // if (strpos($methodCode, Bank::CODE) !== false) {
            // return $methodCode;
        // }

        return $result;
    }
}