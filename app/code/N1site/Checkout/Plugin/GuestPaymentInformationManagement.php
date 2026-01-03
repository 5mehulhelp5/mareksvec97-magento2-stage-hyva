<?php

namespace N1site\Checkout\Plugin;

use N1site\Checkout\Helper\Data;

class GuestPaymentInformationManagement {
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    public function afterGetPaymentInformation(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        $result,
        $cartId
    ) {
		
        $paymentMethods = [];
        foreach ($result->getPaymentMethods() as $method) {

			$code = false;
			if (is_object($method)) {
				$code = $method->getCode();
			} elseif (is_array($method)) {
				$code = $method['code'];
			}
			
			// foreach($method->getData() as $key=>$data) {
				// file_put_contents(__DIR__.'/test2.txt', $key.'::'.print_r((array)$data, true)."\n", FILE_APPEND);
			// }
			
            if ( $code && ($paymentCode = $this->helper->getPaymentConfigPath($code)) ) {
				$paymentMethods[] = [
					'logo_url' => $this->helper->getPaymentLogoUrl($paymentCode),
					'description' => $this->helper->getPaymentDescription($paymentCode)
				];
			}
        }
		
		// if (count($paymentMethods)) {
			// $result->setPaymentMethods($paymentMethods);
		// }

        return $result;
    }
}