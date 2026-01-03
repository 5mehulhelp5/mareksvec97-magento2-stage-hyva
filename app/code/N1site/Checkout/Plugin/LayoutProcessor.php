<?php
namespace N1site\Checkout\Plugin;

use Magento\Framework\View\LayoutFactory;

class LayoutProcessor {
	
    private $layoutFactory;
    private $customerSession;
	
	// private $cart;
	
    // private $helper;
	
	// protected $_checkoutSession;

    public function __construct(
        LayoutFactory $layoutFactory,
		\Magento\Customer\Model\Session $customerSession
		// \Magento\Checkout\Helper\Cart $cart,
		// \N1site\Checkout\Helper\Data $helper,
        // \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->customerSession = $customerSession;
		// $this->cart = $cart;
        // $this->helper = $helper;
		// $this->_checkoutSession = $checkoutSession;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
		
		// $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['renders']['children']
		// file_put_contents(__DIR__.'/sidebar.txt', print_r($result['components']['checkout']['children']['sidebar']['children'], 1));
		// file_put_contents(__DIR__.'/discount.txt', print_r($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount'], 1));

		// $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['additionalClasses'] = 'hidden';
		// $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['company_id']['additionalClasses'] = 'hidden';
		// $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['tax_id']['additionalClasses'] = 'hidden';
		// $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id']['additionalClasses'] = 'hidden';

        // if (isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'])) {
            // $billingAddress = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form'];
			// $billingAddress['sortOrder'] = 10;
            // unset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']);		
			// $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['billing-address-form']['children'][] = $billingAddress;
			
			// file_put_contents(__DIR__.'/shippingAddress.txt', print_r($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress'], 1));
			// file_put_contents(__DIR__.'/billingAddress.txt', print_r($billingAddress, 1));
        // }
		
		// if ($customer = $this->customerSession->getCustomer()) {
			// if ($customer->getData('pn_id')) $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['pn_id']['value'] = $customer->getData('pn_id');
			// if ($customer->getData('tax_id')) $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['tax_id']['value'] = $customer->getData('tax_id');
			
			// if (count($customer->getAddresses())>0) {
				// unset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-form']['children']['b2b']);
			// }
		// }
		
		// file_put_contents(__DIR__.'/layout.txt', print_r($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children'], 1));
		
		// if (isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-form']['children']['b2b'])) {
			// $b2b = $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-form']['children']['b2b'];
			// unset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-form']['children']['b2b']);
			// $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['before-shipping-address']['children']['b2b'] = $b2b;
		// }
		
        // if (isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']['before-place-order'])) {
            // $beforePlace = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']['before-place-order'];
			// $beforePlace['sortOrder'] = 10;
            // unset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']['before-place-order']);		
			// $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['checkout-agreements']['children']['checkout-agreements-children'] = $beforePlace;
			
			// file_put_contents(__DIR__.'/beforePlace.txt', print_r($beforePlace, 1));
        // }		
		
        // $component = [
            // 'component' => 'Magento_Ui/js/form/components/html',
            // 'sortOrder' => 0,
            // 'content' => $this->layoutFactory->create()->createBlock('N1site\Checkout\Block\Cart\Freeshipping')->setTemplate('cart/freeshipping.phtml')->toHtml()
        // ];
		// $result['components']['checkout']['children']['sidebar']['children']['additional']['children']['freeshipping-information'] = $component;
		
        $component = [
            'component' => 'N1site_Checkout/js/view/freeshipping-information',
            'sortOrder' => 2,
        ];
        // $result['components']['checkout']['children']['sidebar']['children']['additional']['children']['freeshipping-information'] = $component;
		$result['components']['checkout']['children']['sidebar']['children']['summary']['children']['freeshipping-information'] = $component;
		
		// file_put_contents(__DIR__.'/test.txt', print_r($result, true), FILE_APPEND);
		
        // $component = [
            // 'component' => 'Magento_Ui/js/form/components/html',
            // 'sortOrder' => 10000,
            // 'additionalClasses' => 'checkout_information_box',
            // 'content' => $this->layoutFactory->create()->createBlock('Magento\Cms\Block\Block')->setBlockId('checkout_contact_box')->toHtml()
        // ];
        // $result['components']['checkout']['children']['sidebar']['children']['checkout-contact-box']['children']['checkout-contact-box2'] = $component;

        if (isset($result['components']['checkout']['children']['sidebar']['children']['additional']['children']['comment'])) {
            $commentBlock = $result['components']['checkout']['children']['sidebar']['children']['additional']['children']['comment'];
			$commentBlock['sortOrder'] = 0;
            unset($result['components']['checkout']['children']['sidebar']['children']['additional']['children']['comment']);		
			// $result['components']['checkout']['children']['sidebar']['children']['summary']['children']['itemsAfter']['children'][] = $commentBlock;
			
			// file_put_contents(__DIR__.'/shippingAddress.txt', print_r($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress'], 1));
			// file_put_contents(__DIR__.'/billingAddress.txt', print_r($billingAddress, 1));
        }
		
        if (isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount'])) {
            $discountBlock = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount'];
			$discountBlock['sortOrder'] = 10;
            unset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount']);		
			$result['components']['checkout']['children']['sidebar']['children']['summary']['children']['itemsAfter']['children'][] = $discountBlock;
			
			// file_put_contents(__DIR__.'/shippingAddress.txt', print_r($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress'], 1));
			// file_put_contents(__DIR__.'/billingAddress.txt', print_r($billingAddress, 1));
        }
		
        if (isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields'])) {
            // unset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children']['company']);	
            // unset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children']['vat_id']);	
            // unset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children']['custom_attributes.custom_field_2']);	
            // unset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children']['custom_attributes.custom_field_3']);	
			// file_put_contents(__DIR__.'/test.txt', print_r($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']['form-fields']['children'], true));
        }

		return $result;
	}

}