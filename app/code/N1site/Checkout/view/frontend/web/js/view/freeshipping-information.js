define([
    'Magento_Ui/js/form/components/html',
    'Magento_Customer/js/customer-data',
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            var cartData = customerData.get('cart');

            if (cartData.freeshipping_info !== undefined) {
                this.updateContent(cartData.freeshipping_info)
            }

            cartData.subscribe(function(updatedCart) {
                this.updateContent(updatedCart.freeshipping_info);
            }, this);
        }
    });
});
