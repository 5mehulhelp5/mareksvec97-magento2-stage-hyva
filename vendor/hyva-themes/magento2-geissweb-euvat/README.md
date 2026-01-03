
# Geissweb_Euvat Compatibility Module for Hyvä Themes
https://www.geissweb.de/eu-vat-enhanced-for-magento-2.html

## Requirements
- Hyvä Themes 1.1.14 or higher (form validation)

## Overview

This compatibility module provides frontend related parts for Hyvä themes.
All Luma based checkouts are supported out of the box by the original module.

**You are welcomed to make any adjustments and send a pull request.**

### Completed

- VAT ID field in account create form
- VAT ID field in address edit form
- Most of the integration options from the original module are implemented (form validation, placeholder, toggle visibility by country, disable validation for selected countries)

### Not implemented yet

- Checkout integration with Hyvä or React checkout
- Form validation rule "require a valid VAT number when the company field is filled in"
- Tooltip for additional information about the VAT ID field

## Installation

### Via packagist.com

Hyvä Compatibility modules that are tagged as stable can be installed using composer via packagist.com:

1. Install via composer `composer require hyva-themes/magento2-geissweb-euvat`
2. Enable module `bin/magento setup:upgrade`

### Via gitlab

For development of or to contribute to a compatibility module, it needs to be installed using composer via gitlab.  
This installation method is not suited for deployments, because gitlab requires SSH key authorization.

1. Install via composer

    Requirement: https://gitlab.hyva.io/hyva-themes/magento2-compat-module-fallback

    Add the repo to your composer.json and install the module:
    ```
    composer config repositories.hyva-themes/magento2-geissweb-euvat git git@gitlab.hyva.io:hyva-themes/hyva-compat/magento2-geissweb-euvat.git
    composer require hyva-themes/magento2-geissweb-euvat:dev-main
    ```
2. Enable module `bin/magento setup:upgrade`

## Implementation

The VAT Number field needs to be included in the corresponding form template.
This module provides a block that can be included in the registration and address edit forms.

### Registration form

To include the VAT Number field in the registration form, place the following code in the registration form where you want the VAT number field to appear.

In your template, for example `app/design/frontend/MyVendor/mytheme/Magento_Customer/templates/form/register.phtml` at the place where you want the field to appear:


```php
$vatFieldBlock = $block->getLayout()->getBlock('account_create.vat_id');
echo $vatFieldBlock
    ->setLabel($block->getAttributeData()->getFrontendLabel('vat_id'))
    ->setValue($block->getFormData()->getVatId())
    ->setForm('account_create') //controls which validation area is applied, see configuration
    ->toHtml()
```

### Address edit form
Same as above, just for the address edit form in customer account.

In your template, for example `app/design/frontend/MyVendor/mytheme/Magento_Customer/templates/address/edit.phtml`, at the place where you want the field to appear:

```php
<?php $vatFieldBlock = $block->getLayout()->getBlock('customer_address.vat_id');?>
<?php if ($addressViewModel->addressIsVatAttributeVisible()): ?>
<?php echo $vatFieldBlock
    ->setLabel($block->getAttributeData()->getFrontendLabel('vat_id'))
    ->setValue($block->getAddress()->getVatId())
    ->setForm('address_edit') //controls which validation area is applied, see configuration
    ->toHtml()?>
<?php endif; ?>
```
