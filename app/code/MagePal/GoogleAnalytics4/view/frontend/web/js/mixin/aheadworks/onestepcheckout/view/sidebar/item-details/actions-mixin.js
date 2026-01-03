define([
    'jquery'
],function ($) {
    'use strict';

    return function (Component) {
        return Component.extend({
            onRemoveClick: function (item) {
                this._super(item);
                $('body').trigger('mpGa4CheckoutItemRemoved', [item.item_id, item.qty]);
            }
        });
    }
});
