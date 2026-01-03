<div align="center">

[![](https://hyva.io/media/wysiwyg/logo-compact.png)](https://hyva.io/)

# Hyvä Themes - Payment Icons

[![Magento Supported Versions]](#)
[![Hyva Supported Versions]](https://docs.hyva.io/hyva-ui-library/getting-started.html)
[![Hyva Themes Module](https://img.shields.io/badge/Hyva_Themes-Module-3df0af.svg?longCache=true&style=for-the-badge)](https://hyva.io/)
[![Figma]](https://www.figma.com/@hyva)
[![License]](./LICENSE.md)

</div>

## Installation

Install the package via:

```sh
composer require hyva-themes/magento2-payment-icons
bin/magento setup:upgrade
```

<details><summary>Installation guide for GitLab access (contributions)</summary>

```sh
composer config repositories.hyva-themes/magento2-payment-icons git git@gitlab.hyva.io:hyva-themes/magento2-payment-icons.git
composer require hyva-themes/magento2-payment-icons:dev-main
bin/magento setup:upgrade
```

</details>

## How to use

Usage example:

```php
<?php

use Hyva\Theme\Model\ViewModelRegistry;
use Hyva\PaymentIcons\ViewModel\PaymentIconsClean;

/** @var ViewModelRegistry $viewModels */

/** @var PaymentIconsClean $paymentIcons */
$paymentIcons = $viewModels->require(PaymentIconsClean::class);
```

and use the PaymentIcons just as the HeroIcons in your phtml:

```php
<?= $paymentIcons->paypalHtml('', 48, 32, ["aria-label" => "Pay with PayPal"]) ?>
```

### Using SVG icons in CMS content

The icons can also be rendered in CMS content, using the `{{icon}}` directive.

Find the path of the SVG inside `view/frontend/web/svg`, and remove the `.svg` at the end.

For instance, `view/frontend/web/svg/payment-icons/clean/paypal.svg` can be used as `payment-icons/clean/paypal`.

Usage example in CMS pages:

```html
{{icon "payment-icons/clean/paypal" classes="inline-block" width=48 height=32 title="Pay with PayPal"}}
```

### Extend and Customization

Please refer to the Hyvä Docs for information about SvgIcon usage in Hyvä Themes: https://docs.hyva.io/hyva-themes/writing-code/working-with-view-models/svgicons.html

## Available icons

There are currently 5 implementations: **Clean**, **Light**, **Dark**, **Mono** and **Outline**.

The available icon render methods can be found at `src/ViewModel/PaymentIconsInterface.php`,
but they will also autocomplete in your editor (if php intellisense is supported by your editor).

<details><summary>List of Available Payment logos in each Icon Style</summary>

affirm • afterpay • algorand • alipay • alma • amazon-pay-2 • amazon-pay • american-express-2 • american-express • apple-pay • atome • atone • au-pay • bancomat • bancontact • bankaxept • belfius • billie • bitcoin • bitpay • blik • boleto • boost-wallet • cartes-bancaires • cash-app • clearpay • click-to-pay • creditcard • dankort • dash • diners-club • discover • divido • eftpos • elo • eps • ethereum • famipay • fonix • forbrugsforeningen • fpx • gcash • giropay • google-pay • gopay • grabpay • hiper • hipercard • ideal • in3 • interac • invoice • iwocapay • jcb • kakao • kbc • klarna • korean-cards • kriya • line-pay • link • litecoin • mastercard-securecode • mastercard • merpay • mobilepay • mondu • monero • naver-pay • nexi-pay • octopus • oney • oxxo • paidy • payco • payconiq • payment-on-delivery • paynow • paypal-2 • paypal • paypay • paypo • paysafecard • picpay • pix • planpay • pledg • postepay • postfinance • prepayment • przelewy24 • rakuten-pay • reown • revolut-pay • ripple • riverty • sage-pay • samsung-pay • satispay • scalapay • sepa • sodexo • sofort • spei • swish • touch-n-go • troy • truelayer • trustly • twint • uatp • unionpay • venmo • vipps • visa-secure • visa • vpay • walley • wechat-pay • wero • western-union • yoco • younited • zapper • zip

</details>

### Preview

| Clean           | Light           | Dark           | Mono           | Outline           |
| --------------- | --------------- | -------------- | -------------- | ----------------- |
| ![PayPal Clean] | ![PayPal Light] | ![PayPal Dark] | ![PayPal Mono] | ![PayPal Outline] |

> Mono and Outline use currentColor and can be customize with your own colors.

[PayPal Clean]: ./view/frontend/web/svg/payment-icons/clean/paypal.svg
[PayPal Light]: ./view/frontend/web/svg/payment-icons/light/paypal.svg
[PayPal Dark]: ./view/frontend/web/svg/payment-icons/dark/paypal.svg
[PayPal Mono]: ./view/frontend/web/svg/payment-icons/mono/paypal.svg
[PayPal Outline]: ./view/frontend/web/svg/payment-icons/outline/paypal.svg

## Other Icon packs

For more Icons packs see our [Hyvä docs](https://docs.hyva.io/hyva-themes/view-utilities/hyva-svg-icon-modules.html) page or the [github topic #hyva-icons](https://github.com/topics/hyva-icons)

## License

Hyvä Themes - https://hyva.io

Copyright © Hyvä Themes B.V 2020-present. All rights reserved.

This product is licensed per Magento install. Please see [License File](LICENSE.md) for more information.

## Disclaimer

All used trademarks, brands and/or names are the property of their respective owners.

The use of these trademarks, brands and/or names does not indicate endorsement of the property holder by us, nor vice versa.

[License]: https://img.shields.io/badge/License-004d32?style=for-the-badge "Link to Hyvä License"
[Magento Supported Versions]: https://img.shields.io/badge/magento-%202.4-orangered.svg?longCache=true&style=for-the-badge "Magento Supported Versions"
[Hyva Supported Versions]: https://img.shields.io/badge/Hyv%C3%A4->=1.1.12-0A23B9?style=for-the-badge&labelColor=0A144B "Hyvä Supported Versions"
[Figma]: https://img.shields.io/badge/Figma-gray?style=for-the-badge&logo=Figma "Link to Figma"
