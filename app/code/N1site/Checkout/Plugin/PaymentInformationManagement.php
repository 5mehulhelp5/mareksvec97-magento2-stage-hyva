<?php

namespace N1site\Checkout\Plugin;

use N1site\Checkout\Helper\Data;
use Magento\Quote\Api\Data\PaymentMethodInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;

class PaymentInformationManagement {
	
    /**
     * @var ExtensionAttributesFactory
     */
    protected $extensionAttributesFactory;
	
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     * @param Data $helper
     */
    public function __construct(
        ExtensionAttributesFactory $extensionAttributesFactory,
        Data $helper
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->helper = $helper;
    }

    public function afterGetPaymentInformation(
        \Magento\Checkout\Model\PaymentInformationManagement $subject,
        $result, // Magento\Checkout\Model\PaymentDetails
        $cartId
    ) {

		$extensionAttributes = $result->getExtensionAttributes();
        /** @var \Magento\Quote\Api\Data\ShippingMethodExtensionInterface $shippingExtension */
        /*
		$paymentExtension = $extensionAttributes ?
            $extensionAttributes : $this->extensionAttributesFactory->create(PaymentMethodInterface::class);
		
		
        $paymentMethods = [];
		
		// file_put_contents(__DIR__.'/test1.txt', get_class($result));

        foreach ($result->getPaymentMethods() as $method) {
			// file_put_contents(__DIR__.'/test1.txt', print_r($method->getCode(), 1));
			
            // $paymentCode = $this->helper->getPaymentConfigPath($method->getCode());
            $logoUrl = $this->helper->getPaymentLogoUrl($method->getCode());
            $description = $this->helper->getPaymentDescription($method->getCode());

            if ($logoUrl) {
				$method->setLogoUrl($logoUrl);
                // $method['logo_url'] = $logoUrl;
				$paymentExtension->setLogoUrl($logoUrl);
            }

            if ($description) {
                // $method['description'] = $description;
				$method->setDescription($description);
				$paymentExtension->setDescription($description);
            }

            $paymentMethods[] = $method;
        }

        $result->setPaymentMethods($paymentMethods);
		$result->setExtensionAttributes($paymentExtension);
		*/

        return $result;
    }
}