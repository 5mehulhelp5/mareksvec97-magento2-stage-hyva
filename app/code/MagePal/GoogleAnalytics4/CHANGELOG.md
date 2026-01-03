1.7.1
=============
* ##### New Features:
    * none

* ##### Fixed bugs:
    * Fix issue with <a href="https://amasty.com/ajax-shopping-cart-for-magento-2.html?a=magepal" rel="nofollow" _blank="new">Amasty Ajax to Cart</a>
    * Fix GA4 add to cart issue when use in combine with our Google Enhanced Ecommerce

1.7.0
=============
* ##### New Features:
    * Add Related, Upsell, cross Sell to Admin config
    * Add support for Enhanced Ecommerce & GA4 to work side by side

* ##### Fixed bugs:
    * Fix issue with Product Detail list element
    * Fix issue with Product Detail position element
    
1.6.0
=============
* ##### New Features:
    * Fix issue capturing the correct product category and position on product detail page
    * Fix add/update/remove product qty for Aheadworks OneStepCheckout mini checkout side cart
    * Add support (partial) for Aheadworks OneStepCheckout
    * Fix wrong url generated for add cart tracking url tracking
    * Fix issue with add to cart with PayPal on product detail page in Magento 2.3.5
    * Add new add to cart events (Missing options and item out of stock events)
    * Add new option for category product impression list type
    * Add currency code to product data push event to better support currency conversion
    * Add Wishlist product impression
    * Add Compare product impression
    * Add new admin option for category product impression list name
    * Add limited support for <a href="https://amasty.com/one-step-checkout-for-magento-2.html?a=magepal" rel="nofollow" _blank="new">Amasty One Step Checkout</a>
    * paymentMethodAdded not triggering on some payment method
    * Add support for <a href="https://amasty.com/ajax-shopping-cart-for-magento-2.html?a=magepal" rel="nofollow" _blank="new">Amasty Ajax to Cart</a>
    * Add checkout events
        * checkoutShippingStepCompleted
        * checkoutShippingStepFailed
        * checkoutPaymentStepCompleted
        * checkoutPaymentStepFailed
        * shippingMethodAdded
        * paymentMethodAdded
        * checkoutEmailValidation
    * Add Javascript Trigger
        * mpGa4CheckoutShippingStepValidation
        * mpGa4CheckoutPaymentStepValidation
    * Fix "class does not exist" when saving admin order
    * Update checkout steps logic to prevent duplicate events
    * Add product variant
    * Add product category (auto select first category)
    * Api to quickly add more content to the data layer (see base GTM for more information)
    * Add jQuery trigger event for mpCustomerSession, mpCartItem, mpCheckout and mpCheckoutOption
    * Add data list to product detail page
    * Edit add to cart item on product detail page
    * Support for Enhanced Success Page
    * Add currency code to product data layer
