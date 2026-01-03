# Changelog - Hyvä Checkout - Stripe Integration

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [Unreleased]

[Unreleased]: https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/compare/1.0.3...main

## [1.0.3] - 2024-02-12

### Added

- Nothing was added.

### Changed

- **Fixed a Stripe element not rendering when the wallet button is turn off**
  
  For more information, please refer to [merge request #14](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/merge_requests/14)

  Many thanks to Jacob Nguyen (JaJuMa GmbH) for the contribution!

- **Fixed throwing an exception after installing strip with no public key**
  
  For more information, please refer to [merge request #15](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/merge_requests/15)

  Many thanks to Tjitse Efdé (Vendic) for the contribution!

### Removed

- Nothing was removed.

## [1.0.2] - 2023-10-24

[1.0.2]: https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/compare/1.0.1...1.0.2

### Added

- Nothing added.

### Changed

- **Return an empty element when the feature is disabled**
  
  For more information, please refer to [merge request #10](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/merge_requests/10).

- **Moved product page payment button to shortcuts**
  
  For more information, please refer to [issue #4](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/issues/4).

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Fix to check if the cart contains contents before loading the cart payment button**
  
  For more information, please refer to [issue #5](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/issues/5).

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Fix for fetching quote information on initialization**
  
  For more information, please refer to [merge request #7](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/merge_requests/7).

### Removed

- **Remove layout instructions**
  
  For more information, please refer to [merge request #9](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/merge_requests/9).

## [1.0.1] - 2023-08-18

[1.0.1]: https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/compare/1.0.0...1.0.1

### Added

- Nothing added.

### Changed

- **Use hyva_ prefixed layout handles**

  Previously regular layout handles were used, causing Luma checkouts in the same instance trying to load Magewire.

  For more information, please refer to [issue #2](https://gitlab.hyva.io/hyva-checkout/checkout-integrations/magento2-hyva-checkout-stripe/-/issues/2).

### Removed

- Nothing was removed.

## 1.0.0 - 2023-08-10

### Added

- Initial Release.

### Changed

- Nothing was changed.

### Removed

- Nothing was removed.
