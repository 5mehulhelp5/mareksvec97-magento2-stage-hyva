/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals'
], function ($, _, quote, totals) {

    function hasPath(obj, path)
    {
        var hasKey = true;

        for (var i = 0, path = path.split('.'), len = path.length; i<len; i++) {
            if (_.has(obj, [path[i]])) {
                obj = obj[path[i]];
            } else {
                hasKey = false;
                break;
            }
        }

        return hasKey;
    }

    var loadDataLayer = function (dataLayer) {
        var _dataLayer = {};
        _.each(dataLayer, function (item) {
            _dataLayer = $.extend(true, _dataLayer, item);
        });

        return _dataLayer;
    };

    var cart = {
        items: [],
        hasChanges: false,
        total: 0,
        setItem: function (item) {
            this.hasChanges = true;
            this.items.push(item);
        },
        init: function (items) {
            this.hasChanges = true;
            this.items = items;
        },
        getItems: function () {
            return this.items;
        },
        removeItem: function (item_id) {
            this.hasChanges = true;
            this.items = _.without(this.items, _.findWhere(this.items, {
                quote_item_id: item_id
            }));
        },
        updateItemQty: function (qty, id) {
            if (qty === 0) {
                this.removeItem(id);
                return true;
            }

            var item =  _.find(this.items, function (o) {
                return o.quote_item_id < id;
            });

            if (_.isObject(item) && _.has(item, 'quantity')) {
                item.quantity = qty;
                return true
            }

            return false;
        },
        getTotal: function () {
            var self = this;

            if (this.hasChanges) {
                this.total = 0;
                _.forEach(this.items, function (item) {
                    if (_.has(item, 'price') && _.has(item, 'quantity')) {
                        self.total += item.price * item.quantity;
                    }

                    if (_.has(item, 'discount') && _.has(item, 'quantity')) {
                        self.total -= item.discount * item.quantity;
                    }
                });

                this.hasChanges = false;
            }

            return parseFloat(this.total);
        },
        getGrandTotal: function () {
            var total;
            if (totals.totals()) {
                total = parseFloat(totals.totals()['subtotal']);
            } else {
                total = this.getTotal();
            }

            var shippingInfo = quote.shippingMethod();

            if (_.has(shippingInfo, 'amount')) {
                total += parseFloat(shippingInfo.amount) - parseFloat(totals.totals()['discount_amount'])
            }

            return total;
        }
    }

    var initTrigger = function (dataLayer, config) {
        var $body = $('body');
        $body.on('mpGa4CheckoutShippingStepValidation', function (event, isFormValid, errors) {
            if (isFormValid) {
                var shippingDetail = quote.shippingMethod();
                var title = '';

                if (shippingDetail !== undefined && _.isObject(shippingDetail)) {
                    if (!_.isEmpty(shippingDetail.carrier_title) && !_.isEmpty(shippingDetail.method_title)) {
                        title = shippingDetail.carrier_title + ' - ' + shippingDetail.method_title;
                    } else if (!_.isEmpty(shippingDetail.carrier_code)) {
                        title = shippingDetail.carrier_code
                    }
                }

                dataLayer.push({
                    'event': 'add_shipping_info',
                    'ecommerce': {
                        'shipping_tier': title,
                        'items': cart.getItems(),
                        'value': cart.getGrandTotal()
                    },
                    '_clear': true
                });

                dataLayer.push({'event': 'checkoutShippingStepCompleted'});
            } else {
                dataLayer.push({
                    'event': 'checkoutShippingStepFailed',
                    'checkout': {
                        'shipping_errors': errors
                    },
                    '_clear': true
                });
            }
        });

        $body.on('mpGa4CheckoutPaymentStepValidation', function (event, isFormValid, errors) {
            if (isFormValid) {
                var paymentDetail = quote.paymentMethod();
                let title = _.has(paymentDetail, 'title') ? paymentDetail.title : paymentDetail.method;

                dataLayer.push({
                    'event': 'add_payment_info',
                    'ecommerce': {
                        'payment_type': title,
                        'items': cart.getItems(),
                        'value': cart.getGrandTotal()
                    },
                    '_clear': true
                });

                dataLayer.push({'event': 'checkoutPaymentStepCompleted'});
            } else {
                dataLayer.push({
                    'event': 'checkoutPaymentStepFailed',
                    'checkout': {
                        'payment_errors': errors
                    }
                });
            }
        });

        $body.on('mpGa4CheckoutEmailValidation', function (event, emailExist) {
            let dlObject = loadDataLayer(dataLayer);

            if (!hasPath(dlObject, 'checkout.email_exist') || dlObject.checkout.email_exist !== emailExist) {
                dataLayer.push({
                    'event': 'checkoutEmailValidation',
                    'checkout': {
                        'email_exist': emailExist
                    }
                });
            }
        });

        $body.on('mpGa4CheckoutItemQtyChanged', function (event, itemId, qty, qtyOriginal) {
            if (_.has(config, 'products') && _.isArray(config.products)) {
                _.each(config.products, function (item) {
                    if (parseInt(item.quote_item_id) === itemId) {
                        var eventName = (qty > qtyOriginal) ? 'add_from_cart' : 'remove_from_cart';

                        if (eventName === 'add_from_cart') {
                            item.quantity = qty - qtyOriginal;
                            dataLayer.push({
                                'event': eventName,
                                'ecommerce': {
                                    'currency': config.currency,
                                    'items': [item],
                                    'value': item.quantity * item.price
                                },
                                '_clear': true
                            });
                        } else {
                            item.quantity = qtyOriginal - qty;
                            dataLayer.push({
                                'event': eventName,
                                'ecommerce': {
                                    'currency': config.currency,
                                    'items': [item],
                                    'value': item.quantity * item.price
                                },
                                '_clear': true
                            });
                        }

                        cart.updateItemQty(item.quantity, item.quote_item_id);
                    }
                });
            }
        });

        $body.on('mpGa4CheckoutItemRemoved', function (event, itemId, qty) {
            if (_.has(config, 'products') && _.isArray(config.products)) {
                _.each(config.products, function (item) {
                    if (parseInt(item.quote_item_id) === itemId) {
                        item.quantity = qty;
                        dataLayer.push({
                            'event': 'remove_from_cart',
                            'ecommerce': {
                                'currency': config.currency,
                                'items':  [item],
                                'value': item.quantity * item.price
                            },
                            '_clear': true
                        });

                        cart.updateItemQty(item.quantity, item.quote_item_id);
                    }
                });
            }
        });
    };

    return function (config) {
        var dataLayer = window[config.dataLayerName];
        cart.init(config.products)

        initTrigger(dataLayer, config)

        dataLayer.push({
            'event': 'begin_checkout',
            'ecommerce': {
                'currency': config.currency,
                'items':  cart.getItems(),
                'value': cart.getGrandTotal()
            }
        });

    }
});
