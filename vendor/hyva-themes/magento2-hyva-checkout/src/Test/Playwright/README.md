# Hyvä Checkout Playwright Tests

## To install playwright and run the tests:

1. `cd src/Test/Playwright`
2. `npm ci`
3. `npx playwright install --with-deps`
4. `echo "PLAYWRIGHT_BASE_URL=https://my.local-dev-instance.test/" > .env`
5. `npx playwright test`

More information can be found in the docs: <https://playwright.dev/docs/intro>

## Preparing the system

The tests expect **Sample Data** to be installed, **Hyvä Theme** and **Hyvä Checkout** to be active and the **Purchase Order** payment method to be enabled.  

(The following instructions assume [n98-magerun2](https://github.com/netz98/n98-magerun2#installation) is installed as `magerun2`)

1. If it isn't present already, install the Hyvä Theme and Hyvä Checkout:  
   `composer require hyva-themes/magento2-default-theme hyva-themes/magento2-hyva-checkout`
2. Install Magento sample data:  
   `bin/magento sample-data:deploy`
3. Run the migrations:  
   `bin/magento setup:upgrade`
4. Set the default theme to Hyvä, either manually in the admin or, in CI, this does the job:

   ```bash
    THEME_ID=$(mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" --silent --skip-column-names -e "SELECT theme_id FROM theme WHERE code = 'Hyva/default'")
    magerun2 config:store:set --scope=stores --scope-id=1 design/theme/theme_id $THEME_ID
   ```
   (The theme is configured using `magerun2` because `bin/magento config:set` complains about invalid path `design/theme/theme_id`)
5. Enable Hyvä checkout, either in the admin or using `magerun2`:  
   `magerun2 config:store:set --scope=stores --scope-id=1 hyva_themes_checkout/general/checkout default`
6. Disable the legacy Magento Captcha, either in the admin or in the terminal:  
   `bin/magento config:set --lock-env customer/captcha/enable 0`
7. Enable the built in Purchase Order payment method, either in the admin or in the terminal:  
   `bin/magento config:set --lock-env payment/purchaseorder/active 1`
8. Compile Hyvä CSS:

   ```bash
   npm --prefix vendor/hyva-themes/magento2-default-theme/web/tailwind ci
   npm --prefix vendor/hyva-themes/magento2-default-theme/web/tailwind run build-prod
   ```
9. Enable developer mode:  
   `bin/magento deploy:mode:set developer`
10. Flush the cache:  
    `bin/magento cache:flush`
