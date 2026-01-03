## Hyvä Themes - Hyvä Checkout

Hyvä Checkout is the prime checkout for Hyvä Themes.

## Installation

### With a license key

1. Configure the Hyvä packagist.com repository as a composer source
   ```sh
  
   # This command adds your key to your projects auth.json file
   # Replace yourLicenseAuthenticationKey with your own key
   composer config --auth http-basic.hyva-themes.repo.packagist.com token yourLicenseAuthenticationKey
   # Replace yourProjectName with your project name
   composer config repositories.private-packagist composer https://hyva-themes.repo.packagist.com/yourProjectName/
   ```
   
2. Require the `hyva-themes/magento2-hyva-checkout` package using composer:
    ```sh
    composer require --prefer-source 'hyva-themes/magento2-hyva-checkout'
    ```

3. Run `bin/magento setup:upgrade`

4. Run tailwind to generate the styles for the checkout, replacing `vendor/hyva-themes/magento2-default-theme/web/tailwind/` with the path to your theme's `web/tailwind` folder:
   ```sh
   npm --prefix vendor/hyva-themes/magento2-default-theme/web/tailwind/ ci
   npm --prefix vendor/hyva-themes/magento2-default-theme/web/tailwind/ run build-prod
   ```

### For technology partners

If you have access to the Hyvä Checkout GitLab repository, you can install it in development environments using SSH key authentication.

1. Ensure your public SSH key is added to your account on gitlab.hyva.io.

2. Add the checkout repository to the Magento `composer.json`
    ```sh
    composer config repositories.hyva-themes/hyva-checkout git git@gitlab.hyva.io:hyva-checkout/checkout.git
    ```
   
3. Require the `hyva-themes/magento2-hyva-checkout` packages using the `dev-main` branch version:
    ```sh
    composer require --prefer-source 'hyva-themes/magento2-hyva-checkout:dev-main'
    ```

4. Run `bin/magento setup:upgrade`

5. Run tailwind to generate the styles for the checkout, replacing `vendor/hyva-themes/magento2-default-theme/web/tailwind/` with the path to your theme's `web/tailwind` folder:
   ```sh
   npm --prefix vendor/hyva-themes/magento2-default-theme/web/tailwind/ ci
   npm --prefix vendor/hyva-themes/magento2-default-theme/web/tailwind/ run build-prod
   ```

## Documentation

* [Hyvä Checkout](https://docs.hyva.io/checkout/hyva-checkout/index.html)
* [Magewire Introduction](https://docs.hyva.io/checkout/hyva-checkout/magewire/index.html)
* [Checkout Developer Documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/index.html)
* [Magewire Documentation](https://github.com/magewirephp/magewire/blob/main/docs/Features.md)

## Security Vulnerabilities

If you discover a security vulnerability within this module, please send an e-mail to
[info@hyva.io](mailto:checkout@hyva.io). All security vulnerabilities will be addressed promptly.

## License

Copyright © Hyvä Themes 2022-present. All rights reserved.  
This product is licensed per Magento install  
See [hyva.io/license](https://hyva.io/license)


