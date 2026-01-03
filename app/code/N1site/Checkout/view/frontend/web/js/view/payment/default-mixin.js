define([
], function () {
	'use strict';
	
	return function (Component) {
		return Component.extend({
			/**
			 * Get payment method logo url
			 */
			getLogoUrl: function () {
				if (window.checkoutConfig && window.checkoutConfig.paymentData && window.checkoutConfig.paymentData[this.item.method] && window.checkoutConfig.paymentData[this.item.method].logo_url) {
					return window.checkoutConfig.paymentData[this.item.method].logo_url;
				}
				return '';
			},

			/**
			 * Get payment method logo url
			 */
			getDescription: function () {
				if (window.checkoutConfig && window.checkoutConfig.paymentData && window.checkoutConfig.paymentData[this.item.method] && window.checkoutConfig.paymentData[this.item.method].description) {
					return window.checkoutConfig.paymentData[this.item.method].description;
				}
				return '';
			}
		});
	}
});