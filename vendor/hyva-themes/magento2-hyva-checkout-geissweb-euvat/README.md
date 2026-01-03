
# Geissweb_Euvat Compatibility Module for Hyvä Themes
https://www.geissweb.de/eu-vat-enhanced-for-magento-2.html

## Requirements
- Hyvä Themes 1.3 or higher (AlpineJS 3)
- Hyvä Checkout 1.1.27 or higher

If you don't meet these requirements, please use the 1.0.0 version of this module. It supports Hyvä Themes 1.1.14 and Hyvä Checkout 1.0.

## Overview

This compatibility module provides frontend related parts for Hyvä Checkout.
All Luma based checkouts are supported out of the box by the original module.

**You are welcomed to make any adjustments and send a pull request.**

## Installation

### Via packagist.com

Hyvä Compatibility modules that are tagged as stable can be installed using composer via packagist.com:

1. Install via composer `composer require hyva-themes/magento2-hyva-checkout-geissweb-euvat`
2. Enable module `bin/magento setup:upgrade`

### Via gitlab

For development of or to contribute to a compatibility module, it needs to be installed using composer via gitlab.  
This installation method is not suited for deployments, because gitlab requires SSH key authorization.

1. Install via composer

    Requirement: https://gitlab.hyva.io/hyva-themes/magento2-compat-module-fallback

    Add the repo to your composer.json and install the module:
    ```
    composer config repositories.hyva-themes/magento2-hyva-checkout-geissweb-euvat git git@gitlab.hyva.io:hyva-checkout/checkout-integrations/magento2-hyva-checkout-geissweb-euvat.git
    composer require hyva-themes/magento2-hyva-checkout-geissweb-euvat:dev-main
    ```
2. Enable module `bin/magento setup:upgrade`

## Credits

A big thank you to the contributors of this module:

- [JaJuMa](https://www.jajuma.de/)
- [Vendic](https://www.vendic.nl/)
