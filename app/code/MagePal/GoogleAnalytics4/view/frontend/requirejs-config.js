var config = {
    map: {
        '*': {
            addToCartGa4DataLayer: 'MagePal_GoogleAnalytics4/js/add-to-cart-datalayer',
            addToCartAjaxGa4DataLayer: 'MagePal_GoogleAnalytics4/js/add-to-cart-ajax-datalayer',
            dataLayerGa4ShareComponent: 'MagePal_GoogleAnalytics4/js/shared-component',
            checkOutGa4DataLayer: 'MagePal_GoogleAnalytics4/js/checkout-datalayer',
            ga4DataLayer: 'MagePal_GoogleAnalytics4/js/datalayer'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'MagePal_GoogleAnalytics4/js/mixin/shipping-mixin': true
            },
            'CyberSource_Address/js/view/cybersource-shipping': {
                'MagePal_GoogleAnalytics4/js/mixin/shipping-mixin': true
            },
            'Magento_Checkout/js/view/payment/default': {
                'MagePal_GoogleAnalytics4/js/mixin/payment/default-mixin': true
            },
            'Magento_Checkout/js/view/form/element/email':{
                'MagePal_GoogleAnalytics4/js/mixin/view/form/element/email-mixin': true
            },
            'Aheadworks_OneStepCheckout/js/view/form/email':{
                'MagePal_GoogleAnalytics4/js/mixin/aheadworks/onestepcheckout/view/form/email-mixin': true
            },
            'Aheadworks_OneStepCheckout/js/view/sidebar/item-details/qty':{
                'MagePal_GoogleAnalytics4/js/mixin/aheadworks/onestepcheckout/view/sidebar/item-details/qty-mixin': true
            },
            'Aheadworks_OneStepCheckout/js/view/sidebar/item-details/actions':{
                'MagePal_GoogleAnalytics4/js/mixin/aheadworks/onestepcheckout/view/sidebar/item-details/actions-mixin': true
            }
        }
    }
};
