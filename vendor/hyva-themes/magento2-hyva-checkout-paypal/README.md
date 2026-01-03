## Hyvä Themes - PayPal Integration

This module enables the use of PayPal with Hyvä Checkout.  
The first release covers only basic functionality for PayPal Express, but still can be used in production and as an example for custom payment integrations.
For supported features, please check the [checkout integration tracker ticket](https://gitlab.hyva.io/hyva-public/checkout-integration-tracker/-/issues/1).

## Installation

Hyvä-Checkout license holders can install the PayPal integration by running

```sh
composer require hyva-themes/magento2-hyva-checkout-paypal
```

## Know issues

Currently the integration only works if the payment step is after the shipping address is specified.  
Retrieving the shipping address from the PayPal account is not yet implemented.

Any issues can be found and/or reported through the [appropriate repository](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-paypal/-/issues) issues section.

## Developer Documentation

* [Checkout Developer Documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/index.html)

## Security Vulnerabilities

If you discover a security vulnerability within this module, please send an e-mail to
[info@hyva.io](mailto:info@hyva.io). All security vulnerabilities will be addressed promptly.

## License

Copyright © Hyvä Themes 2022-present. All rights reserved.  
This product is licensed per Magento install  
See [hyva.io/license](https://hyva.io/license)
