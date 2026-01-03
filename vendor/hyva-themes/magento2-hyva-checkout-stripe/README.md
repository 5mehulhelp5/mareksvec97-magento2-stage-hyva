## Hyvä Themes - Stripe Integration

This module enables the use of Stripe with Hyvä Checkout.

Supported services:

- [X] Checkout Payments
- [X] Button for payment request (Wallet Button)
- [ ] Subscriptions via Stripe
- [ ] Bank transfer (default magento modul)

## Requirements

Before proceeding with the installation, please ensure that you have acquired the [Stripe Payments](https://commercemarketplace.adobe.com/stripe-stripe-payments.html) module from the Adobe Marketplace.
The Stripe Payments module is available for free.

## Installation

Hyvä Checkout license holders can install the Stripe integration by running

```sh
composer require hyva-themes/magento2-hyva-checkout-stripe
```
### Via gitlab

For development of or to contribute to this module, it needs to be installed using composer via gitlab.  
This installation method is not suited for deployments, because gitlab requires SSH key authorization.

1. Install via composer
    If this is the first time a compatibility module is installed via gitlab, the compat-module-fallback git repository 
    has to be added as a composer repository. This step is only required once.
    ```
    composer config repositories.hyva-themes/magento2-compat-module-fallback git git@gitlab.hyva.io:hyva-themes/magento2-compat-module-fallback.git
    ```

    When the compat-module-fallback repo is configured, the compatibility module itself can be installed with composer:
    ```
    composer config repositories.hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe git git@gitlab.hyva.io:hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe.git
    composer require hyva-themes/magento2-hyva-checkout-stripe:dev-main
    ```

## Known issues

Any issues can be found and/or reported through the [appropriate repository](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/issues) issues section.

## Developer Documentation

* [Checkout Developer Documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/index.html)
* [Stripe Documentation](https://stripe.com/docs)

## Security Vulnerabilities

If you discover a security vulnerability within this module, please send an e-mail to
[info@hyva.io](mailto:info@hyva.io). All security vulnerabilities will be addressed promptly.

## License

Copyright © Hyvä Themes 2022-present. All rights reserved.  
This product is licensed per Magento install  
See [hyva.io/license](https://hyva.io/license)
