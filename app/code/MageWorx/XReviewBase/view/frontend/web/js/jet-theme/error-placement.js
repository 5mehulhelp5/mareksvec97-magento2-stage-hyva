define([
    'jquery',
    'mage/mage'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).mage('validation', {
            /** @inheritdoc */
            errorPlacement: function (error, el) {

                if (el.parents('#product-review-table').length) {
                    $('#product-review-table').siblings(this.errorElement + '.' + this.errorClass).remove();
                    $('#product-review-table').after(error);
                } else if (el.parents('.mw-checkbox.policy-wrapper').length) {
                    $('.mw-checkbox.policy-wrapper').siblings(this.errorElement + '.' + this.errorClass).remove();
                    $('.mw-checkbox.policy-wrapper').append(error);
                } else {
                    el.after(error);
                }
            }
        });
    };
});
