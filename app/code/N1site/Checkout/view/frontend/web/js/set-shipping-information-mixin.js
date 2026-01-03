/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
	// 'Magento_Customer/js/model/customer'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }
			
            if (shippingAddress['customAttributes'] === undefined) {
                shippingAddress['customAttributes'] = {};
            }

			
			var pn_id = $('[name="pn_id"]').val();
			var tax_id = $('[name="tax_id"]').val();
			
			// if (pn_id) {
				shippingAddress['customAttributes']['pn_id'] = shippingAddress['extension_attributes']['pn_id'] = pn_id;
					
				// console.log(shippingAddress);
				// customer.customerData['custom_attributes']['pn_id']['value'] = pn_id;
				// customer.customerData['pn_id'] = pn_id;
				// console.log(customer.customerData);
			// }
			
			// if (tax_id) {
				shippingAddress['customAttributes']['tax_id'] = shippingAddress['extension_attributes']['tax_id'] = tax_id;
					
				// console.log(shippingAddress);
				// customer.customerData['custom_attributes']['tax_id']['value'] = tax_id;
				// customer.customerData['tax_id'] = tax_id;
				// console.log(customer.customerData);
			// }
			
			// console.log(pn_id);
			// console.log(tax_id);
			
            return originalAction();
        });
    };
});