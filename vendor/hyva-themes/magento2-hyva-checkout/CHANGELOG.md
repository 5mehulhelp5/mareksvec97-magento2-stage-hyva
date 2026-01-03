# Changelog - Hyvä Checkout

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [Unreleased]

[Unreleased]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.3.4...main

## [1.3.4] - 2025-07-16

[1.3.4]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.3.3..1.3.4

### Added

- **Feature to Set a Custom Redirect Template via Place Order Service**

  A new feature was introduced to allow setting a custom redirect template when placing an order via a custom Place Order Service.

  By using the `withRedirectTemplate(string)` method, developers can now specify a custom `.phtml` template that will render when redirecting after order placement.

  For more information, please refer to [merge request #470](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/470).

  Many thanks to Ole Hornauer (Oval Media) for the contribution!

- **Config to Control Display of Shipping Total Before Method Selection**

  A new configuration option was added to control whether the shipping method total is shown when no shipping method has been selected yet.

  Previously this would show the shipping cost shows as `$0.00` or `Free` which can be confusing for customers.

  By default, this setting is enabled to maintain the existing behavior after upgrade. Disabling it will hide the shipping total until a method is selected:
  `hyva_themes_checkout/design/formatting/show_shipping_method_total_if_none_selected`

  For more information, please refer to [merge request #469](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/469).

  Many thanks to Nick Hall (MFG Supply) for the contribution!

- **Config to Display Carrier Title and Method Name Together**

  A new configuration option was added to allow displaying the carrier title and method name together in the shipping method list.

  This feature is disabled by default to avoid unexpected formatting changes during upgrades. To enable it, set the following config to `true`:
  `hyva_themes_checkout/design/shipping_payment_methods/combine_shipping_method_name`

  For more information, please refer to [merge request #463](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/463).

  Many thanks to Kostadin Bashev (webcode) for the contribution!

- **Added Frontend Data Validation for Telephone Field**

  This update adds frontend validation to all telephone inputs.

  For more information, please refer to [merge request #425](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/462).

  Many thanks to Aad Mathijssen (iO) for the contribution!

### Changed

- **Fix for Missing Evaluation Message on Purchase Order Number**

  A regression was fixed where the evaluation message for the required "Purchase Order Number" field was not displayed when using the "Purchase Order" payment method with an empty field.

  This issue was introduced in the 1.3.0 upgrade and only occurred on fresh quotes — refreshing the page would restore the expected validation behavior.

  For more information, please refer to [merge request #468](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/468).

- **Fix to Prevent Fallback Checkout Errors with Incorrect Theme Fallback Configuration**

  We've added a fix to prevent frontend errors when the Hyvä Checkout is used alongside the Hyvä Theme Fallback module and the fallback configuration is incorrect.

  Previously, if `checkout/index` was listed in the fallback paths, the following errors could occur: `Main wire element could not be found`
  
  This often resulted in the Luma checkout being shown unexpectedly, leading to a poor experience, especially for new customers setting up Hyvä Checkout.

  For more information, please refer to [merge request #467](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/467).

- **Fix for Error When Country Field is Disabled in Checkout Address**

  A fix was added to resolve an error that occurs when the country field is disabled in the checkout address configuration.

  This happened when disabling the country field, for example, if the store only delivers to a single default country. 

  This fix ensures that when the country field is disabled, the checkout correctly uses the default country without triggering this validation error.

  For more information, please refer to [merge request #466](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/466).

  Many thanks to Rik Willems (Actiview) for the contribution!

- **Fix for Missing Use of Translation Function**

  A fix was added to ensure proper use of Magento’s translation functions and phrase collection using the `i18n:collect-phrases` command.

  For more information, please refer to:
  [merge request #401](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/401)
  [merge request #461](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/461)

  Many thanks to Giacomo Moscardini (webgriffe) & Aad Mathijssen (iO) for the contribution!

- **Fix for UnhandledMatchError in `currencyWithLabelingConditions()`**

  A fix was applied to prevent an `UnhandledMatchError` that could occur in `Hyva\Checkout\ViewModel\Checkout\Formatter::currencyWithLabelingConditions()`.

  The error occurred when the `$result` from the currency condition was neither a `string` nor an instance of `Phrase`. 

  A default case was added to ensure fallback behavior by calling the base `currency()` method.
 
  For more information, please refer to [merge request #460](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/460).

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Fix for Address List Items Always Inactive in `list.phtml`**

  A fix was added to address an issue where list items in `address-list/list.phtml` were always inactive in the checkout address selector.

  For more information, please refer to [merge request #458](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/458).
  
  Many thanks to Jeroen Balk (Adwise) for the contribution!

- **Shipping Method List Now Updates When Applying Coupon Codes**

  Previously, when a discount code was applied that enabled free shipping, the updated shipping methods were not reflected in the shipping method list.

  This update enables the listeners to update the shipping method list when a coupon code is applied, eliminating the need for a page reload.

  For more information, please refer to [merge request #356](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/356).

  Many thanks to Ole Schäfer & Simon Sprankel (CustomGento) for the contribution!

### Removed

- Nothing Removed

## [1.3.3] - 2025-06-19

[1.3.3]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.3.2..1.3.3

### Added

- **Add Missing Translations in Checkout**

  This update adds missing translations for the "Contact Information" section label as well as various shipping method error messages.

  For more information, please refer to:
  [merge request #454](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/454)
  [merge request #452](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/452)

  Many thanks to Aad Mathijssen (iO) for the contributions!

### Changed

- **Performance Optimization in Observer Conditional Logic**

  A performance improvement was made by reordering `if` conditions to ensure the least expensive calls are executed first.

  Previously, the observer logic always called `getActiveCheckout()` first, even on pages where it wasn't necessary.

  By reordering the conditionals to short-circuit early, this optimization prevents unnecessary logic from running on every page load.

  For more information, please refer to [merge request #442](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/442).

  Many thanks to Igor Wulff (Youwe) for the contribution!

- **Loader Trigger Behavior Update**

  A fix has been applied to `hyvaCheckout.loader.start()` trigger the checkout loader as expected.

  For more information, please refer to [merge request #455](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/455).

- **Swapped Console Log Evaluation Processor for Executable Result**

  The browser console log mechanism has been refactored from an evaluation processor into a standalone executable result (`checkout.browser.console-log`).

  For more information, please refer to [merge request #453](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/453).

- **Fixed DOM Attribute Reflection for Relative Form Fields**

  Fixed an issue where class attributes (e.g. class-wrapper, class-element) were not being correctly applied to fields rendered relative to a parent form. 

  This ensures visual and behavioral consistency for nested form fields such as multi-line street address inputs. 

  Also, standardized disabled state styling to `disabled:opacity-60` to match other checkout components.

  For more information, please refer to [merge request #451](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/451).

- **Improved Accessibility for Shipping Method Missing Error**

  When no shipping method is selected and the user attempts to proceed to the next step, an error message is displayed. 

  This message was previously not announced by screen readers. This has now been fixed.

  For more information, please refer to [merge request #449](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/449).

- **Fix Default Billing and Shipping Address Checkbox Behavior**

  For logged-in customers "Billing and shipping address are the same" checkbox was incorrectly pre-selected when a customer’s default billing address differed from their default shipping address. 

  Now, the checkbox is only pre-selected when the billing and shipping addresses are the same. 

  If the customer has different default addresses, the checkbox is unchecked, and the correct default billing address is displayed during checkout.

  For more information, please refer to [merge request #448](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/448).

  Many thanks to Olaf Caspers (Trinos) for the contribution!

- **Improved Checkout Step Condition Evaluation Performance and Readability**

  Refactored the `\Hyva\Checkout\Model\Checkout\StepConditions::assertSuccess()` method to improve performance and reduce complexity.

  The new implementation uses a `foreach` loop with early returns, enabling short-circuit evaluation of checkout step conditions.

  For more information, please refer to [merge request #446](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/446).
 
  Many thanks to Jean-Bernard Valentaten (techdivision) for the contribution!

- **Fix: Duplicate `id="shipping"` Preventing Auto-Save Directive from Resolving Correctly**
 
  Resolved an issue where two nodes in the checkout shared the same `id="shipping"`, which caused the Magewire `wire:auto-save` directive to target the wrong element.
 
  For more information, please refer to [merge request #445](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/445).
 
  Many thanks to Tobias Hartmann (Basecom) & Daniel Galla (iMi digital GmbH) for the contribution!

- **Enhanced Street Address Field Labels with Configuration-Aware Context**

  Improved the rendering of street address form fields by adding contextual labels based on system configuration. 

  This change addresses the ambiguity of generic validation messages (e.g., "This field is required") that can arise when street fields are rendered as multi-line inputs.

  By applying clearer labels to each individual line of the street address field, users now receive more intuitive validation feedback.

  For more information, please refer to [merge request #441](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/441).

- **Resolved Tooltip Visibility Issue via z-index Adjustment and Template Tag Consideration**

  Fixed an issue where tooltips could be partially hidden due to overlapping elements. This was caused by insufficient `z-index` layering.
 
  For more information, please refer to [merge request #418](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/418).

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Updated Boolean Object Handling for CSP Compatibility**

  Aligned the checkout implementation with changes introduced in [hyva-themes/magento2-theme-module#454](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/454), which simplifies `hyva.createBooleanObject(...)`.

  This update ensures consistent behavior between CSP and non-CSP environments, resolving the issue where `!something` was incorrectly transformed into a non-function falsy value in non-CSP mode.

  For more information, please refer to [merge request #400](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/400).

- **Prevented Accidental Form Submission in Guest Details Component**

  When the “Enable Login” option is disabled for the guest details component, pressing Enter after entering an email address would submit the form as a GET request to the current URL.

  This update prevents unintended form submissions by explicitly disabling default submit behavior for the component when login is not enabled.

  For more information, please refer to [merge request #450](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/450).

  Many thanks to Daniel Galla (iMi digital GmbH) for the contribution!

- **Improve evaluation timing to ensure all processors and results are registered**

  The evaluation is now triggered on `init:after` instead of `init:evaluation` to allow proper registration of processors and results before execution.

  For more information, please refer to [merge request #457](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/457).

### Removed

- Nothing Removed

## [1.3.2] - 2025-05-09

[1.3.2]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.3.1..1.3.2

### Added

- Nothing Added

### Changed

- **PHP 8.4 Support for Magento 2.4.8**

  This includes missing PHP `8.4` support for two files:

  `src/Magewire/Checkout/Payment/MethodList.php`
  `src/Magewire/Checkout/Shipping/MethodList.php`

  For more information, please refer to [merge request #432](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/432).

  Many thanks to Pieter Hoste (Baldwin) for the contribution!

### Removed

- Nothing Removed

## [1.3.1] - 2025-05-08

[1.3.1]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.3.0..1.3.1

### Added

- **PHP 8.4 Support for Magento 2.4.8**

  Hyvä Checkout is now compatible with PHP `8.4`, Magento/Adobe Commerce `2.4.8` system requirements. This update includes 
  necessary adjustments for deprecations and stricter type enforcement introduced in PHP `8.4`.

  Projects running Magento `2.4.8` can now use Hyvä Checkout with PHP `8.4`.

  For more information, please refer to [merge request #424](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/424).

- **Support for Redirect-Only Payment Methods**

  Hyvä Checkout now supports the `getOrderPlaceRedirectUrl` method in the Quote payment model (`\Magento\Quote\Model\Quote\Payment::getOrderPlaceRedirectUrl`).

  This ensures compatibility with payment service providers (PSPs) that rely on redirect-only payment flows without requiring additional compatibility modules.

  For more information, please refer to [merge request #421](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/421).

  Many thanks to Michiel Gerritsen (Control Alt Delete) for the contribution!

- **PlaceOrderService for Multiple Payment Methods**

  The `PlaceOrderService` now supports handling multiple payment methods dynamically based on their method code. 
  
  Previously, a separate `PlaceOrderService` had to be registered in `etc/frontend/di.xml` for each individual method even 
  when using the same service class.

  This introduces a matcher method in `PlaceOrderService` that allows it to handle multiple payment methods dynamically, 
  based on their method code. This removes the need to register the service per method and keeps the Hyvä integration in sync automatically.

  For more information, please refer to [merge request #420](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/420).

  Many thanks to Michiel Gerritsen (Control Alt Delete) for the contribution!

### Changed

- **Improved Navigation Handling and Alpine Component Support**

  Navigation Tasks can now be scoped to specific navigation directions. Developers can control execution using `whenNavigatingForwards`,
  `whenNavigatingBackwards`, and `whenNavigatingBothWays` methods on the Navigation Task evaluation result.

  Dynamic Alpine.js components now automatically initialize when loaded via Magewire. This ensures alpine components
  such as payment methods behave correctly even when injected dynamically.

  Additionally, the next and previous steps are now injected into the checkout config. This resolves issues where refreshing the
  page on a later step caused the previous step to be unavailable. You can now reliably use `hyvaCheckout.navigation.getNext()`
  and `hyvaCheckout.navigation.getBefore()` by accessing the injected step history via `hyvaCheckout.config.getValue()`.

  For more information, please refer to [merge request #425](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/425)

- **Auto-Save Directive Now Supports Checkboxes and Radio Buttons**

  The `auto-save` directive now correctly detects changes to checkboxes and radio buttons. 

  Previously, it only observed `element.value`, which caused changes to these input types to go unnoticed—preventing the 
  auto-save functionality from triggering.

  With this fix, the directive correctly uses `element.checked` as the value for checkboxes and radios, ensuring auto-save behavior across all input types.

  For more information, please refer to [merge request #419](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/419).

- **Mobile Checkout UX Improvements**

  To improve clarity on mobile devices, the text “Order Summary” has been changed to "Review Your Order." This helps set clearer expectations that further action is required on this step.

  For more information, please refer to [merge request #416](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/416).

  Many thanks to Ryan Hoerr (ParadoxLabs) for the contribution!

- **New Config Option: Show Free Shipping Label**

  A new system configuration setting has been added to optionally display a "Free Shipping" label during checkout.

  **Config path:** `hyva_themes_checkout/component/shipping/free_shipping_label_enabled`

  This allows merchants to easily highlight free shipping options to customers.

  For more information, please refer to [merge request #415](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/415).

- **Fix: `checkout:payment:method-activate` Event Behavior**

  In versions `1.2.0` and lower of Hyvä Checkout, the `checkout:payment:method-activate` event was dispatched when switching 
  from the payment page to the shipping page and then back to payments. A regression was introduced in `1.3.0`. 

  The issue is now resolved, and the event will be properly dispatched again for both shipping/payment methods.

  For more information, please refer to [merge request #414](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/414).
  
  Many thanks to Michiel Gerritsen (Control Alt Delete) for the contribution!

- **ACL Resource Identifier Mismatch in Admin Menu**

  A mismatch in the ACL resource identifier was causing the Hyvä Checkout configuration to disappear from the admin menu when using custom role permissions. 

  With this fix, the Hyvä Checkout configuration will now be visible when using custom role permissions.

  For more information, please refer to [merge request #413](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/413).

  Many thanks to Samuel Jozwiak (Nola Consulting) for the contribution!

- **Fix: Missing Fallback for Email Address Tooltip in Guest Details**

  An issue was identified in the Shipping Address Form component, where if the `Email Address Tooltip` field in the 
  Guest Details component group was left empty while the setting was set to `Yes` it would not fall back to the Luma tooltip text.

  This behavior has been fixed, ensuring that when the `Email Address Tooltip` field is empty, the Luma default tooltip text will be used as a fallback.

  For more information, please refer to [merge request #412](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/412).

  Many thanks to Akif Gumussu (Aquive) for the contribution!

- **Conditional Customer Session ID Regeneration in Checkout**

  The Customer Session ID will now only be regenerated during checkout for insecure requests or requests with an insecure reference.

  This improvement maintains security by ensuring that a Session ID is regenerated only when necessary, specifically in cases where insecure HTTP connections are used in non-checkout pages.

  For more information, please refer to [merge request #393](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/393).

  Many thanks to Pieter Zandbergen (Emico) for the contribution!

### Removed

- Nothing Removed

## [1.3.0] - 2025-03-13

[1.3.0]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.2.0..1.3.0

### Added

- **Checkout CSP Support For UX Redesign**

  Added Strict Content Security Policy (CSP) support for checkout. This version replaces regular Alpine.js with Alpine CSP,
  ensuring compliance with strict CSP rules. As part of this update, the checkout now enforces strict CSP mode by default.

  To get started with CSP, please refer to the [Hyvä Theme developer documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/csp/index.html).

  For more information, please refer to [merge request #411](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/411).

### Changed

- Nothing Changed

### Removed

- Nothing Removed

## [1.2.0] - 2025-03-05

[1.2.0]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.29...1.2.0

### Added

- **Form Field Icon Accessories**

  Support for adding icons inside or next, depending on the form styles, to the form field to visualize the input method.

  For more information, please refer to [merge request #383](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/383)

- **Back To Cart Button**

  Added `Show Back to Cart Button` which can be configured in the admin to appear, this gives the user the ability to
  return to the without needing to use the browser back buttons. (This is disabled by default)

  This setting can be found under **Store > Configuration > Hyvä Themes > Checkout > Navigation > Show Back to Cart Button**.

  For more information, please refer to [merge request #383](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/383)

- **New Tooltip Style**

  A new design option for tooltips now allows content to be displayed beneath the main field for improved clarity and
  accessibility. The classic tooltip style remains available, along with the hint text display option, both of which can
  be managed in the admin settings. These updates enhance usability, accessibility, and aesthetics, ensuring a smoother
  and more user-friendly checkout experience for all users.

  This setting can be found under **Store > Configuration > Hyvä Themes > Checkout > Design > Form Fields > Tooltip Style**.

  For more information, please refer to [merge request #383](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/383)

- **New Breadcrumb Design - Progress bar**

  A new breadcrumb design can now be configured in the admin to appear as a progress bar stepper, providing a more visual
  and intuitive navigation experience. The classic breadcrumb style remains available and can also be managed in the admin settings.
  (This is disabled by default)

  This setting can be found under **Store > Configuration > Hyvä Themes > Checkout > Breadcrumbs > Breadcrumbs display**.

  For more information, please refer to [merge request #383](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/383)

- **Shipping Method Icons**

  Introduced support for adding metadata to shipping methods, enabling the inclusion shipping provider logos.
  Metadata allows shipping methods to be extended with additional frontend features without requiring modifications to
  the Magento shipping method object. This enhancement provides greater flexibility and improved customization options shipping methods.

  For more information, please refer to [merge request #381](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/381)

### Changed

- **Checkout UX Design Refresh**

  Enhancements to the overall checkout experience with a focus on both visual design and accessibility improvements.
  The updated design includes refined visual styles, improved spacing, and clearer form elements, all aimed at making the
  checkout process more intuitive and user-friendly.

  For more information, please refer to [merge request #383](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/383)

- **Improved Translation Support for Navigation Steps**

  Removed the use of `mb_strtolower` for navigation step labels, which previously forced button labels (e.g., "Shipping")
  to display in lowercase. This change ensures that translated phrases retain their correct capitalization and formatting,
  improving support for multilingual storefronts.

  For more information, please refer to [merge request #380](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/380)

  Many thanks to Ole Hornauer (Oval Media) & Martin Nguyen (Jajuma) for the contribution!

- **Correct Missing list semantics**

  Addressed an accessibility issue in Safari where list-style: none removes list semantics, making it inaccessible to screen
  readers. The solution involves adding role="list" or wrapping the list in a `<nav>` element to preserve semantics. Updates
  have been applied to the default theme in v1.3 to ensure compliance and improve accessibility.

  For more information, please refer to [merge request #361](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/361)

- **Fixed Backward Compatibility Issue in 1.1.29**

  In 1.1.29, we introduced the Zero Subtotal Checkout: Hide Other Payment Methods feature. However, this included a backward-incompatible change.

  This issue has now been resolved.

  For more information, please refer to [merge request #408](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/408)

  Many thanks to Pieter Hoste (Baldwin) & Leon Hagendijk (MaxServ) for the contribution!

### Removed

- **Remove redundant main role on hyva-checkout-main for improved accessibility**

  This is a backward incompatible change. Previously, `#hyva-checkout-main` had a `role="main"` attribute, which could affect
  queries relying on `role="main"` for targeting elements. This change was necessary to prevent accessibility issues caused
  by multiple main elements on the page.

  If your customization or JavaScript relies on `role="main"` being present on `#hyva-checkout-main`, update your selectors accordingly.

  For more information, please refer to [issue #383](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/383).

## [1.1.29 csp] - 2025-03-12

[1.1.29 csp]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.29..f038ad7602631739da5c15571b86e0cba8b7369c

### Added

- **Checkout CSP Support**

  Added Content Security Policy (CSP) support for checkout. This version replaces regular Alpine.js with Alpine CSP, 
  ensuring compliance with strict CSP rules. As part of this update, the checkout now enforces strict CSP mode by default.

  To get started with CSP, please refer to the [Hyvä Theme developer documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/csp/index.html).

  For more information, please refer to [merge request #410](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/410).

### Changed

- Nothing Changed

### Removed

- Nothing Removed

## [1.1.29] - 2025-02-21

[1.1.29]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.28...1.1.29

### Added

- **Billing Address Display Configuration**

  Resolved an issue introduced in version 1.1.28 where checking the "My billing and shipping address are the same"
  checkbox no longer copied the shipping address to the billing address. This occurred because the shipping address
  had not yet been saved.

  To address this, we have introduced a new system configuration option:
  `hyva_themes_checkout/developer/fixes_workarounds/render_billing_address`

  When enabled (default), this option removes the shipping address from above the billing address selection component.

  For more information, please refer to [merge request #400](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/400).

  Many thanks to Joost Wanetie (Trinos) & Daniel Galla (imi) for the contribution!

- **Admin Toggle for Field Autosave in Address Forms**

  Enhanced the Shipping and Billing Address Form in the admin area by adding an auto save checkbox for each field.
  When enabled, the field will autosave upon user input. This allows administrators to selectively enable autosaving for
  specific fields.

  For more information, please refer to [merge request #398](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/398)

- **Disable Form Auto Saving Configuration**

  The form auto-saving feature has been a core part of Hyvä Checkout since its inception. As development progressed, more 
  third-party integrators began designing their solutions with auto-saving in mind. Consequently, some third-party systems 
  now depend on this feature, making it essential to keep auto-saving enabled. Disabling it could lead to potential 
  compatibility issues with these integrations.

  To address this, we have introduced a new system configuration option:
  `hyva_themes_checkout/developer/fixes_workarounds/disable_form_auto_saving`

  When enabled, this option completely disables the form auto-saving mechanism, which may impact third-party integrations 
  relying on this functionality. However, if you choose to disable auto-saving, you can still manually configure specific 
  fields to auto-save as needed.

  By default, auto-saving remains enabled, so users upgrading from version `1.1.26` or earlier do not need to take any action.

  For more information, please refer to [merge request #405](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/405)

- **Zero Subtotal Checkout: Hide Other Payment Methods**

  Improved the checkout experience by ensuring that when a customer's cart is eligible for the Zero Subtotal Checkout
  payment method, all other payment options are hidden. This ensures a seamless checkout process by automatically
  applying the zero subtotal payment method to the quote.

  For more information, please refer to [merge request #353](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/353)

### Changed

- **Address Form Keyboard Tabbing Auto-Save Update**

  Resolved an issue where the address form would update when tabbing through fields, even if the country selection
  remained unchanged. This caused a disruptive user experience as the loading process interfered with input in the next
  field. The fix ensures that the address is only updated when relevant changes are made.

  For more information, please refer to [merge request #397](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/397)

  Many thanks to Pim de Brabander (iO) for the contribution!

- **Scroll to Correct Element on Checkout Validation Failure**

  Resolved an issue where the checkout validation correctly identified when required sections were incomplete, but
  failed to scroll to the correct element. This fix ensures that users are automatically directed to the relevant
  section when validation fails, improving the overall user experience during checkout.

  For more information, please refer to [merge request #387](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/387)

  Many thanks to Nathan Day (Wind & Kite) & Adrien Lagrange (Nola Consulting) for the contribution!

### Removed

- **Removing the last form autosave remains**

  An autosave timeout is no longer required moving saving into a Evaluation API navigation validation task. This has now
  been removed from the admin config, please see `\Hyva\Checkout\Magewire\Component\AbstractForm::build()` instead.

  For more information, please refer to [merge request #395](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/395)

## [1.1.28] - 2025-01-23

[1.1.28]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.27...1.1.28

### Added

- Nothing Added

### Changed

- **E-mail Validation Errors**

  Resolved an issue where the email address component would throw `500` errors when the configuration  
  `hyva_themes_checkout/component/guest_details/enable_login` was set to `No`. This error occurred during checkout when an 
  invalid email address was entered and changed to another invalid email before proceeding. The fix ensures that email validation 
  errors are now properly displayed.

  For more information, please refer to [merge request #388](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/388)

  Many thanks to Stijn Jansen (Trinos) for the contribution!

- **Sensitive Details Exposure in URL on Guest Login**

  Resolved a regression from Alpine.js `v3.14.4` and newer that impacted the Guest Details component. This issue specifically 
  affected users who upgraded to `hyva-themes/magento2-theme-module:1.3.10` and had "Enable Login" activated at checkout. 
  The regression caused the first login attempt to fail, exposing sensitive details such as email and password in the URL, 
  which posed a potential security risk. This fix ensures that login attempts are securely processed without exposing credentials.

  For more information, please refer to [merge request #386](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/386)

  Many thanks to Mihai Ardeleanu (innobyte) & Jacques Kirchner for the contribution!

- **Improved Context Usage for Payment Method Availability**

  Enhanced the `isAvailable()` function for payment methods by passing the current quote model when it is already available.
  This improvement allows payment methods to consider the current quote context when determining their availability, enabling
  dynamic adjustments based on quote details. For example, the "Zero Subtotal Checkout" payment method (`method code: free`)
  relies on the quote context to determine its availability. This update ensures more accurate payment method visibility
  across various scenarios.

  For more information, please refer to [merge request #378](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/378)

- **No Price Displayed for Shipping Methods with Default Tax Settings**

  Resolved an issue where Magento's default tax settings caused no price to be displayed for shipping methods. This was due to
  the `/view/frontend/templates/checkout/shipping/method-list.phtml` template lacking a fallback price. The fix ensures that
  shipping methods always display a price, even when the default tax settings are in use.

  For more information, please refer to [merge request #379](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/379)

- **Corrected Sort Order with "Enable First Available Shipping Method Automatically"**

  Resolved an issue where enabling the experimental feature "Enable first available shipping method automatically" caused the
  sort order of shipping methods to be ignored. This resulted in the wrong shipping method being preselected instead of the
  first method in the sorted list. This fix ensures that the preselected shipping method now correctly follows the defined sort order.

  For more information, please refer to [merge request #377](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/377)

  Many thanks to Michael Wegner for the contribution!

### Removed

- Nothing removed

## [1.1.27] - 2025-01-13

[1.1.27]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.26...1.1.27

### Added

- Nothing added

### Changed

- **Read `hyva_checkout.xml` Files from All Frontend Themes**

  Resolved an issue where custom checkout configurations in a theme's `etc/hyva_checkout.xml` file were ignored unless the
  theme was active or an ancestor of the active theme. This fix ensures that checkout configurations are correctly loaded
  across multiple store views, regardless of the theme used to populate the cache.

  For more information, please refer to [merge request #376](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/376)

  Many thanks to Ruben Zantingh (E-Tales) for the contribution!

### Removed

- **Removed automatically saving for Magewire driven forms**

  Until version `1.1.27`, auto-saving forms in Hyvä Checkout was an automated process. This ensured that form data was
  always synchronized with the server, optionally required by other components like the price summary.
  While this approach guaranteed all data was readily available at all times, we realized that only a small subset of data
  is typically necessary for the checkout to function properly. Therefore, we removed the auto saving feature on forms
  and have adjusted this concept, giving developers greater control over which data is stored.

  For more information, please refer to [merge request #375](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/375)

## [1.1.26] - 2024-10-04

[1.1.26]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.25...1.1.26

### Added

- **Automatic Selection of First Payment and Shipping Method**

  After careful consideration, we've introduced a feature that automatically selects the first payment and shipping
  methods upon entering the checkout. These options can be enabled or disabled independently and are initially placed
  under an experimental flag.

  We’ve done this intentionally, as issues may arise when the first method relies on JavaScript, potentially missing
  expected triggers and causing errors. We recommend enabling these settings only if you're confident your payment
  or shipping methods (e.g., those built with Magewire) can handle them.

  Both settings can be found under **Store > Configuration > Hyvä Themes > Checkout > Developer > Experimental**.

  Please note, these features are disabled by default and must be enabled manually.

  For more information, please refer to [merge request #158](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/158)

### Changed

- **Scope Parameter for Terms and Conditions Configuration**

  A scope parameter has been added to the terms and conditions configuration lookup to resolve an issue where other T&C settings were required.

  For more information, please refer to [issue #339](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/339)

  Many thanks to Bert ter Heide (iO) for the contribution!

- **Evaluation Error Message validation remove listener**

  A bug slipped in where when a Evaluation Error Message result was used, the validation stayed in place event when the user
  would interact with the component to solve the issue which cause the validation error message in the first place. A listener
  has been added to the Error Message result type processor, to check if the component can remove that particular validation.

  For more information, please refer to [merge request #363](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/363)

  Many thanks to Tobias Hartmann (basecom GmbH & Co. KG) for the contribution!

- **Experimental Fix for Magewire Evaluation Hydrator**

  We’ve introduced an experimental fix for the Magewire Evaluation Hydrator. Previously, when an update request was
  made to the Main component, evaluation results from all child components were merged and sent with the response to
  ensure the checkout recognized their states.

  This addressed some niche issues where for example, after failing to accept the terms and conditions and attempting
  to place an order, an error would occur. If the user accepted the terms and tried again, the Main component would
  incorrectly recheck the conditions, leading to an endless validation loop unless the page was refreshed.

  The fix now wraps the child result merging in a system setting (enabled by default), preventing the merge with the Main component’s response.

  You can find this setting under **Store > Configuration > Hyvä Themes > Checkout > Developer > Experimental**.
 
  No action is required when upgrading to this or later versions.

  For more information, please refer to [issue #329](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/329)

  Many thanks to Daniel Galla (iMi digital GmbH) for the contribution!

### Removed

- Nothing removed

## [1.1.25] - 2024-09-20

[1.1.25]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.24...1.1.25

### Added

- **Cascading Step Validation**

  We've introduced a new feature that validates each step in the multi-step checkout process. This ensures that all
  required steps are completed and no components requiring specific validations trigger errors or exceptions.
  This enhancement is part of a broader initiative to reduce the data stored in session, giving users more flexibility
  to navigate through the checkout process.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **Configurable Redirect timeout to Order Success Page**

  Previously, customers were automatically redirected to the order success page after three seconds. This behavior is
  now fully configurable, allowing you to set a custom redirect timeout that suits your needs.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **Enhanced Custom Evaluation Result Types**

  Creating a custom evaluation result type tailored to your specific needs now comes with more refined controls.
  We've introduced two new result capabilities that allow you to orchestrate your workflows with greater precision:

  `TaggingCapabilities`: This feature allows you to tag a result type with a unique identifier, enabling better grouping or further distinction.
  `SequenceCapabilities`: Added to help orchestrate tasks by ensuring a navigation step is executed before proceeding with further navigation or order placement.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **Added a MessageDialog Evaluation Result Type**

  As of version 1.1.18, a new `hyvaCheckout.message.dialog()` JavaScript API was introduced, providing developers
  with a universal way to display a dialog box. This includes a title, text, cancel- and confirmation button,
  all of which can be extended with custom callbacks. Now, this functionality can also be triggered from the PHP side,
  allowing you to return a `MessageDialog` evaluation result from your component. This is useful for scenarios such as
  handling validation failures or guiding customers through the next steps in the checkout process.

  This API has been further extended with powerful methods, notably the `withConfirmationCallback()` method, which
  allows you to inject an `Executable` evaluation result type, that runs when the customer presses the confirmation
  button—bringing powerful backend-driven control to your front-end dialog interactions.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **New `Sequence` Capability for Validation Result Type**

  We've added a new `Sequence` capability to the existing `Validation` evaluation result type, giving developers more
  control over the order of validations. This allows you to specify whether a validation should be run before or after another,
  prioritizing certain checks as needed.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **Introducing the `modify()` Method for Enhanced Form Customization**

  With the new `modify()` method, you can now apply additional form modifications by passing a callable.
  This is particularly useful in scenarios where a form has been instantiated and requires adjustments without the
  need for a separate form modifier object.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **Navigator ServerMemoConfig object**

  Hyvä Checkout now allows developers to pass data from the backend to the frontend with every Magewire round trip.
  In addition to Step History data, it now supports Navigator data, which includes details about the current and previous steps.
  This data is automatically synced with the `hyvaCheckout.config` API, ensuring that developers always have access to
  the latest Navigator data on the frontend, for example by using `hyvaCheckout.config.getValue('navigator.history.current')` to
  get the route name of the current step.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

### Changed

- **Expanded Functionality for Batch Evaluation Results**

  The Batch evaluation result has taken on a more significant role in results that can be carried with a Magewire component state.
  To support this, we've extended this result type with new methods, giving developers more flexibility when working with batches.
  You can now search for specific elements or determine if something should be added using the following methods: `filter()`, `containsFailureResults()`, and `containsSuccessResults()`.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **Enhanced Custom Evaluation Result with Argument Support**

  As the need for custom evaluation results grew, we saw an opportunity to enhance this result type further.
  Developers now have the flexibility to add additional argument data using the new `withArguments()` method, offering
  more control and customization options.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

- **Email input group validation bugfix**

  Version 1.1.19 introduced a new advanced form validation feature for Hyvä Checkout called `validateGroup`.
  This addition allows developers to validate only fields assigned to a specific group, avoiding unnecessary validation
  of optional fields.
  
  For example, a password field should be validated when pressing the login button,
  but not when placing an order if the user is already registered by email. This feature helps streamline navigation
  between steps without over-validating irrelevant fields. A bug related to this feature has also been identified and fixed.

  For more information, please refer to [merge request #349](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/349)

### Removed

- **Deprecation of Blocking Evaluation Result in Favor of Error Message Result**

  Since the initial release, a blocking evaluation result type has been available in the core product,
  allowing developers to completely disable the primary navigation button. However, it placed the responsibility on
  developers to provide additional feedback, such as error notifications.
  
  While blocking seemed useful for preventing further actions, it often left customers confused about what to do next.
  
  To improve user experience, we’ve decided to deprecate the blocking result and replace it with an error message result type.
  Now, instead of blocking, the system will display a default notification instructing customers that they must interact
  with the checkout to proceed. If a blocking result previously included a specific "cause", that text will be shown instead
  of the default one, providing clear and direct guidance.

  For developers currently using the Blocking result, no immediate action is required when upgrading to this version of the checkout.
  The transition will happen automatically. However, we strongly encourage developers to update their implementations by
  replacing the Blocking result with an Error Message for a clearer and more user-friendly experience.

  For more information, please refer to [merge request #357](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/357)

## [1.1.24] - 2024-09-10

[1.1.24]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.23...1.1.24

### Added

- **Guest Details for Virtual-Only Checkout**

  The Guest Details component, introduced in version 1.1.12 for most checkout variants,
  is now also available for the virtual-only checkout.

  For more information, please refer to [issue #340](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/340)

  Many thanks to Vladyslav Sikailo (run_as_root GmbH) for the contribution!

### Changed

- **SVG Rendering and Icon Description Improvements**

  SVG rendering has been improved by utilizing HTML width and height attributes,
  and missing icon (SVG) descriptions have been resolved.

  For more information, please refer to [merge request #290](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/290)

### Removed

- Nothing removed

## [1.1.23] - 2024-08-14

[1.1.23]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.22...1.1.23

### Added

- **Re-instated a Collect Totals for Order Summary (experimental)**

  We conducted extensive research on the necessity of triggering a totals recollection for the order summary component.
  Randomly applying totals recollection in your code is not recommended, and we strongly discourage it. To ensure the
  correct rendering of the order summary, we implemented the recollection at a single point in the code.

  Note: This has been done cautiously, with a conditional statement that allows it to be disabled if issues arise during upgrades.
  We are actively working on a more robust solution, with an upgrade to Magewire V3 likely being the best approach.
  This upgrade will avoid single requests for each emit listener by batching them into a single request.

  For more information, please refer to [merge request #327](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/327)

### Changed

- **Comprehensive i18n/en_US.csv Translation Fixes**

  Fixed various translation-related issues, including avoiding null coalescing operators, correcting grammar,
  removing superfluous entries, adding missing frontend translations, and resolving escaping problems in the
  `i18n/en_US.csv` file.
  
  For more information, please refer to [issue #328](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/328),
  [issue #327](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/327),
  [issue #326](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/326),
  [issue #325](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/325) and 
  [issue #324](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/324)

  Many thanks to Aad Mathijssen (iO) for the contribution!

- **Checkout Address Reset on Page Reload**

  Fixed an issue where reloading the checkout page resets customer address information to the default address, caused by
  changes in the navigation flow that incorrectly reassigned the customer's saved information during the checkout process.

  For more information, please refer to [issue #312](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/312)

  Many thanks to Antoni Tormo (Onestic) for the contribution!

- **Non-clickable Previous Breadcrumbs Steps**

  Fixed an issue where, in a multi-step checkout with more than two steps, previous breadcrumb steps were not clickable.

  For more information, please refer to [merge request #350](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/350)

  Many thanks to Antoni Tormo (Onestic) for the contribution!

- **Terms & Conditions Header Display on Empty List**

  Fixed an issue where the Terms & Conditions header was displayed even when no terms and conditions were available.
  The component now has its own dedicated space within the Quote Actions component, including its own Messenger.

  For more information, please refer to [issue #303](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/303)

- **Place Order Button Not Working Due to Missing Default Shipping Address**

  Fixed an issue where the 'Place Order' button would not work if a customer had a default billing address but no
  default shipping address, due to failed validation of null shipping address fields. Customers had to reload the
  page or revisit the checkout for the button to function properly.

  For more information, please refer to [issue #256](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/256)

  Many thanks to Zaahed Yaqubi (Vendic) for the contribution!

### Removed

- Nothing removed

## [1.1.22] - 2024-07-31

[1.1.22]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.21...1.1.22

### Added

- Nothing added

### Changed

- **Company Field Configuration Respect**

  Added functionality to ensure the company field respects the visibility configuration setting.

  For more information, please refer to [issue #322](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/322)

  Many thanks to Mitchel van Kleef (Made By Mouses) for the contribution!

- **Address Modal Closing During Interaction**

  Changed the address modal behavior to prevent it from closing when a user clicks on an input field immediately
  after selecting a country in the dropdown menu during checkout. The modal will now stay open, ensuring a smoother interaction.

  For more information, please refer to [issue #321](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/321)

  Many thanks to Jeroen Noten (iO) for the contribution!

- **Correct Handling of Address Prefixes**

  Fixed the address prefix field handling. Previously, selecting a prefix would save numeric values to the database.
  Now, prefixes are saved and displayed correctly as text.

  For more information, please refer to [issue #320](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/320)

  Many thanks to Daniel Galla (iMi digital GmbH) for the contribution!

- **Support for Special Characters in Payment Method Titles**

  Added support for special characters in payment method titles, such as "&", to prevent checkout malfunctions.
  Titles with special characters are now handled correctly.

  For more information, please refer to [issue #315](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/315)

  Many thanks to Twana Gul (Cream) for the contribution!

- **Error with Empty Translation Strings**

  Fixed an issue where an empty translation string caused an error during the generation of the translation dictionary.
  The process will now complete successfully, even with empty strings.

  For more information, please refer to [issue #306](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/306)

  Many thanks to Federico Arroyo (Growlobby) for the contribution!

- **Exception in CountryAttributeField Method**

  Fixed an exception error in the checkout process related to the `getOptions()` method in the `CountryAttributeField` class.
  This method now correctly handles its arguments, preventing the error. Alongside options are now stored in memory to
  prevent all options being fetched multiple times.

  For more information, please refer to [issue #282](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/282)

  Many thanks to Maxim Ganses (PHPro) for the contribution!

- **Enhanced Payment Method Image Icon Rendering**

  Enhanced the `Hyva\Checkout\Model\MethodMetaData\IconRenderer` class to fully support rendering both SVG icons and
  additional image formats alongside payment method names. Alongside, changes were made to the abstraction preparing
  it to be migrated to shipping methods also.

  For more information, please refer to [issue #279](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/279)

- **Saving Changes to Custom EAV Attributes**

  Fixed an issue where not all changes to custom EAV attributes in the system configuration were being saved when
  updating multiple attributes at once in the shipping/billing address forms. All modifications are now reliably saved.

  For more information, please refer to [issue #268](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/268)

  Many thanks to Fronius for the contribution!

- **Corrected Billing Address Street as String Handling**

  Fixed an issue where, in some scenarios, the billing street data was passed as a string to the address save service,
  causing an exception and preventing the correct saving of billing street information. The street handling has now been
  migrated from the shipping save service to the billing save service to resolve this issue.

  For more information, please refer to [issue #192](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/192)

  Many thanks to Daniel Galla (iMi digital GmbH) for the contribution!

- **Upgrade to Magewire v1.11.1**

  Magewire version 1.11.1 has been released, addressing specific requirements to resolve [issue #321](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/321)..
  For comprehensive details on this release, please visit the [official release page](https://github.com/magewirephp/magewire/releases/tag/1.11.1).

  For more information, please refer to [merge request #337](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/337)

- **Re-introduced Step and Layout Classes for Main**

  During release 1.1.18, the checkout process was updated to manage steps using the Navigator, replacing the previous
  session-based approach. An observer that handled two additional classes for the main component was not migrated to
  use the Navigator, resulting in missing classes. This issue has now been resolved, reintroducing the `step-{step_name}`
  and `step-layout-{layout_name}` "css" classes.

  For more information, please refer to [merge request #332](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/332)

### Removed

- Nothing removed

## [1.1.21] - 2024-07-12

[1.1.21]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.20...1.1.21

### Added

- **Added `modifyFields()`, `modifyElements()`, `modifyField()`, and `modifyElement()` methods for Forms**

  Added four new methods to the `Hyva\Checkout\Model\Form\AbstractEntityForm` class to simplify the application of
  repeated changes to all fields and/or elements, as well as to individual fields and/or elements.
  This enhancement reduces code repetition, making form modifiers more readable, and allows changes to be applied
  without checking for their existence, further clarifying and streamlining form modifiers.

  Optionally, the new methods handle nested item relatives.

  For more information, please refer to [merge request #288](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/288)

### Changed

- **Added Missing Label to "Save in Address Book" Checkbox**

  Added a missing `<label>` tag to the "Save in address book" checkbox for better accessibility and screen reader compatibility.

  For more information, please refer to [issue #316](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/316)

  Many thanks to Ole Hornauer (Oval Media) for the contribution!

- **Added Text to Address-List Form Field Labels**

  Fixed the address-list form fields by adding descriptive text to the labels to ensure proper identification and usability.

  For more information, please refer to [issue #301](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/301)

  Many thanks to Adrian Wyganowski (Snowdog) for the contribution!

- **Implemented ARIA State Attributes for Steps**

  Implemented ARIA state attributes for steps to provide assistive technologies with the correct status information.

  For more information, please refer to [issue #300](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/300)

  Many thanks to Adrian Wyganowski (Snowdog) for the contribution!

- **Added Descriptive Labels to SKU Fields in Cart Items**

  Added descriptive labels to SKU fields in cart items to improve clarity and accessibility for users.

  For more information, please refer to [issue #298](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/298)

  Many thanks to Adrian Wyganowski (Snowdog) for the contribution!

- **Improved Breadcrumb Text Color Contrast**

  Adjusted the breadcrumb text color to improve contrast and readability, enhancing accessibility for users with visual impairments.

  For more information, please refer to [issue #307](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/307)

- **Corrected Improper Use of Header HTML Element**

  Corrected the improper usage of the `<header>` HTML element to conform with semantic HTML standards.

  For more information, please refer to [issue #297](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/297)

  Many thanks to Adrian Wyganowski (Snowdog) for the contribution!

- **Added Form Label to Coupon Field**

  Added a form label to the coupon field to ensure it is properly identified and accessible.

  For more information, please refer to [issue #294](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/294)

  Many thanks to Adrian Wyganowski (Snowdog) for the contribution!

- **Tooltip Toggleable by Click**

  Updated the tooltip functionality to be toggleable by click, improving user interaction and accessibility.

  For more information, please refer to [issue #221](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/221)

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Fixed Incorrect Autocomplete Values in Checkout**

  Fixed the autocomplete settings in the checkout form to display correct and relevant suggestions.

  For more information, please refer to [issue #214](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/214)

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Restored Focus to Trigger Element After Closing Dialog**

  Ensured that after closing a dialog, the focus returns to the trigger element for a seamless user experience.

  For more information, please refer to [issue #195](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/195)

  Many thanks to Adrian Wyganowski (Snowdog) for the contribution!

### Removed

- Nothing removed

## [1.1.20] - 2024-07-08

[1.1.20]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.19...1.1.20

### Added

- Nothing added

### Changed

- **Fixed Uninitialized Class Properties in Checkout Model**

  Class properties `$name`, `$label`, and `$hash` in `\Hyva\Checkout\Model\Checkout` were missing default values,
  leading to a $name must not be accessed before initialization exception on new checkout installations.
  This fix assigns default values to these properties, resolving the issue.

  For more information, please refer to [issue #314](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/314)

### Removed

- Nothing removed

## [1.1.19] - 2024-07-04

[1.1.19]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.18...1.1.19

### Added

- **Introducing `validateGroup` Method for Selective Field Validation**

  A new `validateGroup(group)` method has been introduced to the guest details form. This method will later be migrated
  to all other checkout forms. Additionally, a merge request for the theme module is planned to potentially make this
  feature globally available for [Advanced Form Validation](https://docs.hyva.io/hyva-themes/writing-code/form-validation/javascript-form-validation.html).

  This feature enables validation of only the necessary fields required to proceed to the next step or place the order,
  initially for the Guest details component. For example, if a customer enters their email address and is identified as
  an existing customer but does not fill in the password, clicking "Login" will require the password field to be mandatory.
  However, this requirement will not apply when attempting to navigate to the next step.

  This allows you to add a `data-validate-group` attribute to the element. For example, if the value of this attribute is
  `guest-details`, you can validate only these fields by calling `validateGroup('guest-details')`.

  For more information, please refer to [merge request #315](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/315)

### Changed

- **Reapplied Body Checkout Name Classes (since 1.1.18)**

  Significant changes have been made to the step management system for checkout and its steps in release 1.1.18.
  The session-based approach has been deprecated, which means that those who relied on it will no longer receive the correct data.
  This issue affected the body classes, where an observer added the checkout name as a class alongside inherited classes.
  This problem has now been resolved.

  For more information, please refer to [merge request #313](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/313)

  Many thanks to Cian Castillo (DevTeam Outsourcing) for the contribution!

- **Reverted Development Test Code for Displaying Exceptions in Production**

  While testing the new notification dialogs for displaying Magewire-specific messages instead of using a
  Magewire-embedded modal for exceptions, we overlooked reverting the test code that checks if the system
  is in production mode. This has now been corrected. As a result, you will see an exception message in
  developer mode and a customer-friendly error notification in production.

  For more information, please refer to [merge request #309](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/309)

- **Added Missing Z-Index to Backdrop for Terms and Conditions**

  A `z-30` class was added to the Terms and Conditions modal backdrop.

  For more information, please refer to [merge request #303](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/303)

- **Unescaped Terms and Conditions Text**

  Previously, the Terms and Conditions text was always escaped, which prevented the use of HTML elements such as href
  and line breaks. This limitation has been addressed by implementing the `getIsHtml()` method, ensuring that text is
  only escaped when necessary.

  For more information, please refer to [issue #299](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/299)

- **Fixed Guest Order Name Storage Issue in Magento 2.4.3-p3**

  Resolved an issue where guest orders placed on Magento 2.4.3-p3 did not store the customer's first and last names in
  the database, resulting in these fields being set to NULL. This caused the names to be omitted from the order view in
  the admin, displaying "Guest" instead. This behavior was inconsistent between the Hyvä checkout and the Luma checkout.

  For more information, please refer to [issue #288](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/288)

  Many thanks to Stepans Mihailisins (Arcmedia AG) for the contribution!

- **Fixed Translation Issue with "Save in Address Book" Label**

  Resolved an issue where the "Save in address book" string in the form was not translating correctly.

  For more information, please refer to [issue #295](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/295)

  Many thanks to both Louis de Looze (eWings nv.) and Simon Sprankel (CustomGento GmbH) for the contribution!

- **Fixed Back Button Navigation Issue from External Pages**

  Resolved an issue where pressing the browser's back button on an external page, redirected users to the
  `/checkout/cart` page instead of `/checkout`.

  For more information, please refer to [issue #274](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/274)

  Many thanks to Thijs de Witt (Trinos) for the contribution!

### Removed

- Nothing removed

## [1.1.18] - 2024-06-07

[1.1.18]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.17...1.1.18

### Added

- **Universal Messaging Dialog**

  We’ve introduced a new `hyvaCheckout.message.dialog()` API to provide a native method for delivering essential
  information to customers about specific processes. This dialog can queue multiple messages, allowing users to
  navigate through them using pagination.

  The message dialog replaces numerous modals cluttering the DOM tree and bloating the HTML. We plan to transition
  certain existing message modals to this new universal format in the near future. All Magewire error `confirm()`
  dialogs have already been replaced.

  For more information, please refer to [merge request #304](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/304)

- **Executed after for Navigation Tasks on Place Order**

  We’ve included a automatic dispatch of the `executeAfter()` for Navigation Task evaluation results that are injected
  by native or custom Place Order Services. This ensures that every injected navigation task will only be executed
  after the place order process has succeeded. Previously, this had to be done manually; now it has been automated.
  For instance, if you want to display a 3DS verification dialog via JavaScript right after the order is placed but
  before the customer is redirected to the success page, this is now handled automatically by default.

  For more information, please refer to [merge request #286](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/286)

- **Navigation history**

  Thanks to Magewire, navigating between steps feels like using a SPA (Single Page Application) without any page loads.
  Now, with the introduction of a URL route-driven step management system (see: Session-driven checkout config removal),
  we can make great use of the native browser History API. Each successful step is pushed into the history,
  allowing customers to move back to a previous step using the browser's forward and backward buttons.

  For more information, please refer to [merge request #305](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/305)

### Changed

- **Session-driven checkout config removal**

  A major task we’ve been anticipating for a while has finally been addressed. Before this release, the navigation state
  of the checkout was stored within a specific session, which was automatically managed as soon as the customer entered
  the checkout or navigated between steps.

  This has been completely removed, meaning navigation state data is no longer stored in the session; it is now managed
  by the URL route step parameter. We’ve ensured backward compatibility, so customers using a session-driven checkout
  won’t lose their state during deployment.

  For more information, please refer to [merge request #305](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/305)

- **Step condition `remove` fix**

  We’ve introduced significant improvements to the `hyva_checkout.xml` converter in `1.1.17` release, enhancing features
  like multi-level checkout inheritances. During this refactor, a bug was introduced where conditional layout handle
  items were still applied even if they were marked with `remove="true"`. We released a patch the day after the initial
  release, and this fix has now been integrated natively, eliminating the need for the patch.

  For more information, please refer to [merge request #285](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/285)

  Many thanks to Ronak Limbachiya (Bliss Web Solution Pvt. Ltd) for the contribution!

### Removed

- Nothing removed

## [1.1.17] - 2024-05-13

[1.1.17]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.16...1.1.17

### Added

- **Checkout step `clone` attribute**
  
  We've introduced the capability to clone steps from a particular checkout without requiring it to be explicitly
  defined as a parent. This feature empowers you to craft a checkout without the necessity of inheriting from a parent
  while still being able to include one of its steps seamlessly, eliminating the concern of managing data changes.
  
  Steps can be cloned by utilizing the `{checkout_name}.{step_name}` value for the `clone` attribute within a `step` element.
  
  It's worth noting that overwriting data such as the label, layout, and so forth is still feasible.
  
  For more information, please refer to [merge request #278](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/278)

- **Coupon `code` for applied and revoked Magewire emit events**

  Now, the actual coupon code is included in both the `coupon_code_applied` and the `coupon_code_revoked` Magewire emit events.
  This enhancement grants developers the ability to listen for an emit event within JavaScript using `Magewire.on('{event_name}')`
  and effectively interact with the coupon code provided by the customer.

  For more information, please refer to [merge request #272](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/272)

### Changed

- **Form field previous value improvements**

  We encountered some edge cases where the previous value did not align correctly with the current value set in the field.
  This discrepancy resulted in conflicts when attempting to make form changes based on a comparison of value changes.
  To resolve this issue and enhance reliability, we have implemented adjustments to the Form API.

  For more information, please refer to [merge request #266](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/266)

  Many thanks to Tjitse Efdé (Vendic) for the contribution!

- **Multi-level checkout inheritance improvements**

  We encountered an issue where inheriting from a checkout more than two levels deep caused problems in copying all data
  from upper level checkouts, rendering it ineffective. Essentially, although inheriting from a parent checkout was
  already possible without restrictions, only the checkout data would be inherited, with elements such as parent layout
  update handles left out. We've addressed this issue along with other minor edge cases.

  For more information, please refer to [issue #253](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/253)

  Many thanks to Rajeev K Tomy (integer_net) and Edwin Bos (Cream) for the contribution!

- **Order success message visibility fix for mobile**
  
  Under normal circumstances, after a successful order placement, customers are presented with a view that includes a
  message indicating redirection within a few seconds. This behavior also applied to mobile users.
  However, if the "place order" button was located towards the bottom of the screen, the viewport wouldn't automatically
  scroll to the top to provide users with a clear view of the impending action. This issue has now been resolved.

  For more information, please refer to [issue #272](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/272)

  Many thanks to Arko Rietdijk (Betersport) for the contribution!

- **Replaced the 404 response config dialog**

  We've enhanced the user experience by replacing the confirmation dialog in the `404` Magewire update response with a sleek Hyvä modal.

  For more information, please refer to [issue #284](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/284)

### Removed

- Nothing removed

## [1.1.16] - 2024-04-11

[1.1.16]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.15...1.1.16

### Added

- **Magento 2.4.7 support**

  We've upgraded to Magewire `1.11.0` to support Magento `2.4.7`.

  For more information, please refer to [merge request #241](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/241)

- **Form renderers Image, URL, Reset and Radio**

  We've added new form options to create elements such as images, URLs, and reset buttons, along with the radio field.
  The 'reset' form button is inherited from the button template, while the others now have their own templates.

  For more information, please refer to [merge request #271](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/271)

### Changed

- **Navigation button UTF-8 label support**

  We've updated the button label echoing from using `strtolower` to `mb_strtolower` with `UTF-8` encoding.

  For more information, please refer to [issue #54](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/54)

  Many thanks to Marcus Venghaus (Freelance) for the contribution!

- **Fixed a non-Latin1 component messenger bugfix**

  We've resolved a hashing issue in the messenger component, enabling the hashing of non-Latin1 characters.

  For more information, please refer to [issue #255](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/255)

  Many thanks to Baldwin Agency for the contribution!

- **Telephone field display and require bugfix**

  The EAV customer address telephone attribute object was not responsive to system configuration settings that toggle
  the display of the field and/or its requirement.

  For more information, please refer to [issue #269](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/269)

  Many thanks to Mykola Orlenko (integer_net GmbH) for the contribution!

- **Upgraded the "Save in address book" form element**

  We've updated the 'Save in address book' feature to utilize the latest Form API rendering. Now, the checkbox is
  rendered using the checkbox template, resulting in a significantly smaller component template and greater
  composability via layout XML.

  For more information, please refer to [issue #267](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/267)

### Removed

- Nothing removed

## [1.1.15] - 2024-03-14

[1.1.15]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.14...1.1.15

### Added

- **Multi tab out-of-sync notification modal**

  Implemented a new feature that actively monitors navigation changes, such as proceeding forwards or backwards within steps,
  or completing an order transaction. This enhancement includes the introduction of an 'out of sync' notification modal,
  which promptly alerts users in inactive browser tabs when multiple checkout sessions are concurrently active across different tabs.

  For more information, please refer to [issue #251](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/251)

  Many thanks to Robert Duffner (FindCanary) for the contribution!

- **Missing Cash On Delivery method template**

  Integrated the missing Cash On Delivery method template, allowing for seamless rendering of any backend system-provided instructions.

  For more information, please refer to [issue #254](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/254)
  
  Many thanks to Adam Sarkadi (Baldwin) for the contribution!

- **Evaluation executables callback params**

  Since version `1.1.13`, significant enhancements have been made to our Evaluation API, particularly focusing on flexibility and functionality.
  Notably, we've introduced the capability to inject JavaScript-driven executables, which are essentially registered callbacks on the frontend.
  These callbacks execute based on instructions from the Magewire component, enriching the frontend experience.
  In this release, we've further refined this feature by adding support for passing arguments to these executable callbacks,
  thereby enhancing their versatility and utility.

  For more information, please refer to [issue #259](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/259)

- **Top destinations for country select**

  Administrators within the Magento system have the capability to designate top destinations, which were previously not
  reflected in the country select dropdown on checkout address forms. In this update, we've introduced the functionality
  to automatically include these specified countries at the top of the list, followed by all others.

  For more information, please refer to [issue #234](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/234)

  Many thanks to Dennis Volkering (Web4exposure) for the contribution!

### Changed

- **Re-introduction of the save address checkbox**

  In response to user feedback, we have reinstated the 'save address' checkbox, inadvertently removed since release `1.1.13`.

  For more information, please refer to [issue #258](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/258)

  Many thanks to Nick Ottens (Elgentos) for the contribution!

- **Blocking evaluation result unblocking fix**

  When encountering a blocking evaluation result type, the primary navigation buttons were previously disabled to
  prevent customers from clicking them, ensuring data integrity and accuracy. However, this resulted in potential
  navigation restrictions for users even after resolving any issues. In this release, we've addressed this by
  implementing a mechanism where blocking result types can now dynamically unblock themselves upon receiving a
  different result from the server. This allows for smoother navigation and order placement,
  ensuring a more seamless user experience.
  
  For more information, please refer to [issue #262](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/262)

  Many thanks to both Matthijs Perik (Ecomni) and Klaas van der Weij (iO) for their contribution!

- **Tax detail extension attribute type annotation naming violation**

  We've resolved a type annotation naming violation that previously triggered errors in Swagger.

  For more information, please refer to [issue #246](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/246)

  Many thanks to André Flitsch (Pixel Perfect) for the contribution!

- **Enterprise: Fixed a Gift Wrapping summary total rendering issue**

  We've successfully addressed an issue where the rendering of the gift wrapping summary total would break due to the
  inability to locate an extension attribute block caused by a wrong return type. This problem had broader implications,
  but we've rectified it without introducing any backward incompatible changes.

  For more information, please refer to [issue #249](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/249)

  Many thanks to James Anderson (United Wheels) for the contribution!

### Removed

- Nothing removed

## [1.1.14] - 2024-03-04

[1.1.14]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.13...1.1.14

### Added

- Nothing added

### Changed

- **Password validation for guest authentication**

  With the unveiling of the new Guest Details component, existing customers now have the opportunity,
  under specific circumstances, to authenticate using their email addresses if they are already present in the database.

  However, the password field previously imposed the same stringent password strength validation as the one utilized
  on the registration form. We have since revised this requirement, making it required only.

- **Reversion of Breaking Change in Place Order Services Abstraction**

  During the release of version 1.1.13, an unintended breaking change occurred wherein the Place Order Service abstraction
  unexpectedly returned a `false` result instead of the expected `true`.
  Consequently, this release rectifies the issue, eliminating the necessity for a previously released patch.

### Removed

- Nothing removed

## [1.1.13] - 2024-02-23

[1.1.13]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.12...1.1.13

### Added

- **Frontend Config + Storage APIs**

  In this update, two new objects have been added to the Frontend API, enabling developers to access specific configurations
  injected from the backend. Additionally, the Storage API now provides a mechanism for storing data in the browser's session storage.
  Notably, this data is automatically integrated into the Place Order Service, facilitating payment integrators in securely
  passing credit card information to the service without the need for server-side storage, thereby ensuring PCI compliance.

- **Clickable abstract form element**

  Introducing an abstraction for all clickable form elements, applicable to various elements including URLs and buttons of all types.

- **Magewire abstract form component**

  The Magewire component abstraction has undergone a complete overhaul, integrating all insights gathered regarding form requirements,
  including action execution and modifications. This refined abstraction layer has already been applied in the Guest Details component,
  released in version [1.1.12](https://gitlab.hyva.io/hyva-checkout/checkout/-/releases/1.1.12), and is set to migrate to the current
  shipping and billing forms, enhancing their functionality without necessitating overwriting the object itself.

  This updated abstraction remains fully backward compatible with existing forms while introducing several new modification hooks,
  such as `form:mount:magewire`, `form:execute:submit:magewire`, `form:data:updating:magewire`, and `form:data:updated:magewire`.
  
  Additionally, it incorporates a dynamic hook for clickable form elements, enabling custom methods like `form:execute:{custom_method_name}:magewire`.
  For instance, a custom button such as a "Sign In" button can trigger the `form:execute:sign_in:magewire` form modification hook.

- **YesNoForce config source model**

  An addition of a custom `YesNoForce` source model provides admin select elements with an extra 'Force' option.

- **Three Evaluation API capability traits**

  Three new Evaluation API capability Traits have been introduced for utilization in custom Evaluation Results.

  The first capability, `StackingCapabilities`, enables the execution of evaluation results at specific positions.
  For example, if you need to execute two frontend functionalities, with one preceding a core functionality and the
  other following it, you can use the `withStackPosition()` method to achieve this.

  The second capability, `BlockingCapabilities`, allows a custom evaluation result to block primary navigation buttons
  by simply utilizing the `asBlocking()` method.

  The final capability, `DispatchCapabilities`, empowers developers to dispatch a particular evaluation result immediately
  after the Magewire XHR response returns to the frontend. This capability enables actions such as displaying a specific message directly,
  rather than waiting for validation failure during the customer's attempt to proceed to the next step or place an order.

- **Six Evaluation API result types**

  The Evaluation API has been an integral part of the core from the outset, primarily serving to evaluate a Magewire component
  during both page rendering and updates. Components can optionally implement the EvaluationInterface, allowing them to convey a
  "recipe" to the frontend, which a processor would then execute accordingly.
 
  For instance, this could involve displaying an error message due to an unfilled field as soon as a customer clicks on
  a primary button like the Place Order button.

  Six new Evaluation result types have been introduced, providing developers with greater flexibility to provide specific
  instructions to the frontend. Among these, the `Batch` result type is particularly noteworthy.
  It allows for the return of multiple instructions instead of just one, reducing the number of XHR requests to the backend.
  
  Alongside important types such as `NavigationTask`, `Redirect`, `Validation`, `Executable`, and `Custom`, the Batch result
  type stands out for its efficiency in frontend instruction management.

- **SessionStorage ServerMemoConfig object**

  Introducing the 'Server Memo Config' feature, which empowers developers to inject configuration values from the backend
  into the Frontend API immediately upon the Frontend API booting on the frontend. This capability allows for the injection of data,
  which can now be retrieved using the `hyvaCheckout.config.getValue()` method. Specifically, the `SessionStorage` object injects
  an empty array for both 'shipping' and 'payment'. These arrays serve as storage for specific payment and shipping information,
  which will ultimately be seamlessly transferred to the Order Place Service.

- **Redirect notification and configuration dialog**

  The Evaluation Redirect result type empowers developers to implement a Redirect awareness notification or confirmation dialog.
  With the notification, customers are promptly informed about an impending redirect. In contrast, the confirmation dialog offers
  customers the option to either confirm or dismiss the redirection.

- **Redirect Evaluation API system config settings**

  This functionality enables store admins to configure Redirect dialog settings, including options such as enabling
  notification and confirmation dialogs, specifying the text for notification and confirmation messages,
  and setting the default duration for dialog visibility.

- **`After` containers for each frontend API object**

  Certain JavaScript functionalities necessitate or should extend specific Hyva Checkout APIs. In each API section,
  there is now a `@internal` annotation marked assisting PHTML files, ensuring their execution before any third-party code.
  By utilizing these after containers, developers can inject custom code with precision, ensuring that all the required 
  components are in place.

- **Memoize available regions**

  A slight enhancement has been implemented to optimize the performance by memoizing the available regions for a particular country.
  Previously, this process would entail fetching them multiple times. Now, it is executed just once and stored in memory
  for the duration of the running session, resulting in improved efficiency.

  For more information, please refer to [issue #245](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/245)

### Changed

- **Main component placeOrder `$data` argument**

  The `Hyva\Checkout\Magewire\Main` component, responsible for functions such as step navigation and order placement,
  now has the ability to accept data from the frontend, which it subsequently passes to the associated Place Order Service.
  This enhancement allows developers to store data in browser storage rather than solely on the server.
  
  Notably, session storage is automatically transmitted to the placeOrder method within the Main component.

- **Place Order Service extended with the Evaluation API**

  The Evaluation API has been expanded into the Place Order Service, enriching its functionality to offer developers
  capabilities similar to those available for regular checkout components. Consequently, the place order processor is
  now equipped to transmit a Redirect result type to the frontend, directing it to a designated URL while presenting a
  confirmation dialog beforehand.

  Another critical aspect of this extension is the ability to instruct the component to execute specific JavaScript
  after the order has been placed. This feature is particularly useful for scenarios such as displaying a 3DS authentication
  modal before redirecting to the PSP payment- or order success page.

- **URL form element extends from Clickable**

  Given that a URL functions as a clickable HTML element, the form element has been extended to inherit from Clickable.

- **PlaceOrderServiceProcessor marked as @internal**

  The `Hyva\Checkout\Model\Magewire\Payment\PlaceOrderServiceProcessor` is now designated as `@internal`, indicating to
  developers that they should refrain from utilizing it for their own payment methods and instead leave the order placement
  to the Main component.

- **`isVisible` wrapped for form select elements**

  A visibility bug concerning form select elements, which lacked a wrapping `if` statement to determine whether the
  element should be rendered, has been resolved.

- **`messagesSuccessListener` component messenger event listener**

  The `Hyva_Checkout::page/messenger.phtml` component accessory, which may be found within the shipping and billing forms,
  now has the capability to listen for a success event. This enhancement empowers developers to hide the message using
  JavaScript when it is no longer necessary.

- **AbstractPlaceOrderService `getData()` method added**

  The place order service has been enhanced with a `getData()` method, which retrieves a `Hyva\Checkout\Model\Magewire\Payment\AbstractOrderData`
  object containing the data passed when the customer pressed the Place Order button.
  This method facilitates retrieval of payment data stored in the session storage.

- **Fixed totals return segment items of type TotalSegmentInterface**

  An issue has been addressed wherein the `\Hyva\Checkout\ViewModel\Checkout\PriceSummary\TotalSegments::getTotals()` method
  was expected to return an array of `TotalSegmentInterface[]`. However, it previously returned `array[]` due to a sorting
  operation applied to the final array.

  For more information, please refer to [issue #231](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/231)

  Many thanks to Simon Sprankel (CustomGento) for the contribution!

### Removed

- Nothing removed

## [1.1.12] - 2024-02-06

[1.1.12]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.11...1.1.12

### Added

- **Guest login component**

  We're happy to unveil our latest addition: a sleek new component inspired by the familiar guest login feature found in the Luma checkout.
  This component seamlessly integrates both an email address field and an optional password field.
  The password field will dynamically appear if the email address corresponds to an existing customer and/or if the
  login feature is enabled in the backend component configuration.

  For more information, please refer to [merge request #240](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/240)

### Changed

- Nothing changed

### Removed

- Nothing removed

## [1.1.11] - 2024-02-02

[1.1.11]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.10...1.1.11

### Added

- **Prices now include both inclusive and exclusive tax**

  In this update, inclusive (incl.) and exclusive (excl.) tax prices are now concurrently displayed across various
  components, including shipping methods and the price summary, when the configured backend settings are applied.
  Unlike the previous versions, that limited visibility to either inclusive or exclusive tax prices, the enhanced
  feature allows for the simultaneous presentation of both. The inclusive tax price takes precedence as the primary
  figure, with the exclusive price displayed as its child, appropriately labeled with the exclusive tax name.

  For more information, please refer to [issue #177](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/177)

  Many thanks to Jeroen Balk (Adwise) for the contribution!

- **Form element accessory rendering**

  We've extended the so-called "form element accessory" functionality giving the option to render your own accessory,
  which can be a label, tooltip or comment. Accessories may be seen as support elements to clarify e.g. inputs helping
  the customer to make better choices when interacting with the form. Out of the box, we now support the rendering of
  labels, tooltips, before- & after container. The `renderAccessory` method has now been opened up.

- **Added a "Billing as shipping address" emit event**

  Regardless of the toggle value for 'My billing and shipping address are the same', the billing component will now
  consistently emit a `billing_as_shipping_address_updated` event, providing a point of observation within
  other Magewire components.

  For more information, please refer to [issue #219](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/219)

  Many thanks to Ryan Hoerr (Paradox Labs) for the contribution!

### Changed

- **Corrected display of shipping price after a page refresh**

  Resolved a bug in the shipping price display, where selecting a shipping method initially resulted in an accurate
  summary with correct shipping prices. However, upon refreshing the page, the displayed price would change to
  an incorrect value. This issue has been identified and successfully addressed in this update.

  For more information, please refer to [issue #200](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/200)

  Many thanks to Dave Kleijn (Accent Interactive) for the contribution!

- **Price totals summary update after address switch**

  Resolved an issue where the price summary failed to update when selecting an address with varying tax rates,
  such as from a different country. This fix ensures that the summary now promptly updates when the customer switches
  to an address with distinct tax conditions.

  For more information, please refer to [issue #235](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/235)

  Many thanks to Aad Mathijssen (iO) for the contribution!

- **Price totals summary update shipping costs upon saving the address**

  Addressing [issue #235](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/235), price summary display has
  been rectified to accurately reflect the correct prices.

  For more information, please refer to [issue #224](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/224)

  Many thanks to Nick Hall (MFG Supply) for the contribution!

- **Price totals summary update on billing address select**

  In certain scenarios, the price total summary needed an update when a billing address was saved or an existing
  billing address was selected. To address this, we've introduced two new Magewire listeners: `billing_address_saved`
  and `billing_address_activated`. These listeners ensure that the price total summary is now appropriately updated in
  response to these billing address events.

  For more information, please refer to [issue #239](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/239)

  Many thanks to Aad Mathijssen (iO) for the contribution!

- **Checkbox renderer now includes a before and after container**

  The checkbox PHTML, unlike other form input renderers, previously lacked a before and after rendering container,
  preventing the inclusion of additional DOM elements right before or after a checkbox input form element.
  This limitation has now been addressed.

  For more information, please refer to [issue #220](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/220)

  Many thanks to Jeroen Balk (Adwise) for the contribution!

- **Form modifications added multiple times during runtime**

  Previously, the form initialization within the `__construct` method led to the unintended application of form
  modifiers multiple times, causing an exception that was silently logged into the exception log.
  To address this, we have now relocated the form initialization to the `boot` method. This adjustment ensures
  that the issue no longer occurs, preventing unnecessary exceptions in the future.

  For more information, please refer to [issue #237](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/237)

  Many thanks to Dimitri Robert (Horace) for the contribution!

### Removed

- Nothing removed

## [1.1.10] - 2024-01-09

[1.1.10]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.9...1.1.10

### Added

- Nothing added

### Changed

- **Fixed configurable shipping address form auto-save timeout**

  Unlike the billing address form object, the `getAutoSaveTimeout` method in the shipping address form class previously
  overlooked the system configuration setting. It was hardcoded with a return value of `3000`. This issue has been
  rectified by updating the method to dynamically retrieve the value from the backend configuration.

  For more information, please refer to [merge request #227](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/227)

  Many thanks to Simon Sprankel (CustomGento) for the contribution!

- **Address attribute requirement no longer supersedes the system configuration**

  We've modified both the shipping- and billing form grid configurations. With this update, these settings
  will now take precedence over the `enabled` and `required` value within the attribute settings themselves.
  Among other things, this allows the designation of a second street field as a required field.

  _Important Note: During the upgrade, review both shipping- and billing form
  configurations to ensure that all necessary fields are properly set as enabled and required._

  For more information, please refer to [issue #201](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/201)

- **Enterprise street lines support**

  The display of street lines was previously determined by inspecting the `customer/address/street_lines` configuration
  setting. This approach has been updated, and the number of street lines is now determined by the
  `\Magento\Customer\Helper\Address::getStreetLines()` method, ensuring compatibility with Adobe Commerce.

  For more information, please refer to [issue #175](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/175)

  Many thanks to Aad Mathijssen (iO) for the contribution!

- **Fixed waiting on events that do not listen on event**

  Resolved an issue in the backend component where using `emitTo` caused a malfunction in the 'place order' button functionality.
  This update ensures that `pendingEmits` is correctly populated when using `emitTo`, resolving the issue.

  For more information, please refer to [merge request #225](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/225)

  Many thanks to Kamil Balwierz (Snowdog) for the contribution!

- **Missing translations**

  Missing translations have now been added to the `en_US.csv` dictionary.

  For more information, please refer to [issue #210](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/210)

  Many thanks to Nicola Spadari (Bitbull) for the contribution!

- **Fixed region field visibility issue**

  Resolved an introduced in Hyvä Checkout 1.1.8 by MR [210](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/210), where
  configuring the `general/region/display_all` setting to `0` (the "Allow to Choose State if It is Optional for Country" admin setting)
  no longer hid the region input for countries where states are optional.  
  Instead, a required text input was displayed.

  For more information, please refer to [merge request #227](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/227)

  Many thanks to Aad Mathijssen (iO) for the contribution!

- **Fixed a "Unexpected token '}'" address list issue**

  Introduced a `0` fallback value in case the `getId()` method returns an empty value.
  This prevents the generation of invalid JavaScript.

  For more information, please refer to [issue #225](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/225)

  Many thanks to Yevhen Hlinskyi (AIDALAB) for the contribution!

- **Form input values trailing whitespaces trim**

  In the past, trailing whitespace in form input values was allowed, occasionally leading to problems
  during the save process. To address this, trailing whitespace is now automatically
  trimmed from input values when using the `setValue()` function, mitigating potential issues during data storage.

  For more information, please refer to [issue #217](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/217)

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Enhanced the shipping method list radio button positioning**

  Previously, radio buttons were vertically centered based on the combined height of both the header and the shipping
  method content block, resulting in the radio button appearing as detached or "free-floating."
  This has been resolved by repositioning the radio button adjacent to the shipping method title, ensuring a more
  cohesive and visually aligned layout.

  For more information, please refer to [issue #213](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/213)

  Many thanks to Christoph Hendreich (In-session) for the contribution!

- **Fixed cart unfolding issue**

  We've corrected an issue where the `$canUnfold` variable was inaccurately set solely based on the
  "Unfold" configuration value, disregarding the specified cart item limit threshold. With this fix, `$canUnfold` is 
  now accurately determined by both the "Unfold" configuration and the cart item count, ensuring it is set to `true` only
  when both conditions are met and `false` otherwise.

  For more information, please refer to [merge request #219](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/219)

  Many thanks to Nick Hall (MFG Supply) for the contribution!

### Removed

- Nothing removed

## [1.1.9] - 2023-12-05

[1.1.9]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.8...1.1.9

### Added

- **Payment methods list refresh on coupon code apply or revoke**

  The payment methods list will now try to refresh itself as soon as a coupon code is applied or revoked.

  For more information, please refer to [merge request #213](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/213)

### Changed

- **Fix backward navigation for guests**

  In Release `1.1.8`, a bug was identified that prevented guests from navigating back until all navigation tasks,
  including form validation, were fully resolved. The issue stemmed from our frontend API's inability to access
  data from previous steps. This release resolves the issue.

  For more information, please refer to [merge request #212](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/212)

  Many thanks to Marcus Venghaus (Freelancer) for the contribution!

- **Zipcode requirement by country bugfix**

  We implemented zipcode requirements based on the selected country during the early stages of our Form Modification API.
  At that time, the framework lacked support for multiple scenario hooks. In a later phase, we introduced additional
  hooks without modifying the Zipcode requirement form modifier.  
  This change resolves the issue so the zipcode field is now marked as required or not correctly.

  For more information, please refer to [issue #222](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/222)

  Many thanks to Cian Castillo (Vetshop group) for the contribution!

### Removed

- Nothing removed

## [1.1.8] - 2023-11-03

[1.1.8]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.7...1.1.8

### Added

- Nothing added

### Changed

- **Street fields value spreading bugfix**

  In Release `1.1.7`, a bug was introduced where the wire:model field attribute would receive the value `{field_id}.{field_id}.0`
  instead of the expected `{field_id}.0`. This issue affected all fields that had one or multiple related elements assigned to them.
  Consequently, Magewire was unable to correctly distribute values among these related elements, causing them to be merged into a
  single value within the parent field.

- **Active CSS class isn't set for shipping- and/or billing address listing**

  The active class should consistently apply to logged-in customers with one or multiple addresses in their address book.
  However, this was not the case due to a discrepancy in the `x-data` attribute, where the active address ID was rendered as a string,
  causing it to fail the strict comparison against the expected int value.

  For more information, please refer to [issue #207](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/207)

  Many thanks to Antoine Fontaine (Dn'D) for the contribution!

- **Tax summary doesn't show correct tax amount**

  In Release 1.1.7, a new feature was introduced, allowing the rendering of price summary item extension attributes through a layout XML renderer.
  However, the initial renderer we introduced had a calculation error for each rate, which has now been corrected.

  For more information, please refer to [issue #206](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/206)

  Many thanks to Chathura Maduranga (Supply App) for the contribution!

- **State/Province fields required status not updated as per country configuration**

  The region field was not adhering to the store configuration settings for determining when it should be required.
  Instead, it solely relied on the 'require' value set in the region attribute. This issue has been resolved.

  For more information, please refer to [issue #205](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/205)

  Many thanks to Nathan Day (Wind and Kite) for the contribution!

### Removed

- Nothing removed

## [1.1.7] - 2023-10-31

[1.1.7]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.6...1.1.7

### Added

- **Added a named navigation class to each button**

  Every button in the primary navigation bar now automatically gets a named class to target each button specifically.

  For more information, please refer to [merge request #97](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/97)

  Many thanks to Alice Dean (Fisheye a Youwe company) for the contribution!

- **One column row Street field renderer**

  In a prior release, we introduced an optional street fields renderer. When activated in the configuration, this renderer
  displays the specified street fields in a grid structure. Now, you have the option to choose a second renderer type that
  we've dubbed the "One Column Row" renderer, which will present the street fields in a single row.

  For more information, please refer to [merge request #201](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/201)

- **Form input `hidden` renderer**

  This release introduces a `hidden` input renderer which is used for hidden input form fields.

  For more information, please refer to [merge request #195](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/195)

### Changed

- **Price summary item extension attribute render option**

  In the past, different tax rules were shown as a single item in the totals summary component. We've expanded the
  rendering to automatically include a summary item extension attributes when added as a renderer item via layout XML.
  This enhancement is already in place for tax rules, which now display the rule label, price, and percentage.

  For more information, please refer to [issue #199](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/199)

  Many thanks to Chathura Maduranga (Supply App) for the contribution!

- **Fixed checkout hash issue**

  We've resolved an issue where customers would experience a checkout reset when the `hyva_checkout` cache was cleared.
  This occurred because a unique hash was associated with each generated checkout. The hash would only update if modifications
  were made via `hyva_checkout.xml`. However, since all layout handles with array keys were generated through `uniqid`,
  they were different each time. Now the array keys are predictable and won't change.

  For more information, please refer to [merge request #203](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/203)

- **Frontend API `hyvaCheckout.config`**

  This release introduces a frontend configuration API that allows directly injecting frontend configuration
  settings from PHP. This API operates in a manner akin to the store configuration, offering functions like `getValue()`
  and `isSetFlag()`. To inject configuration settings, you can use the `ServerMemoConfigInterface`. Notably, this configuration
  object is passed as the first argument in each `initialize()` method, ready for use during frontend initialization.

  For more information, please refer to [merge request #195](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/195)

- **Added form fields attribute method `removeAttributesStartingWith`**

  We've introduced a new method `removeAttributesStartingWith` that is used by all form fields. It allows developers
  to eliminate all attributes associated with form fields that begin with a specified string.
  For instance, all Magewire-related attributes can now be removed in a single operation.

  For more information, please refer to [merge request #195](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/195)

- **Rewrote Messenger Component**

  We've revamped the Messenger Component API that was originally part of the messenger.phtml template.
  Now it can be utilized within custom components, without relying on the messenger template.
  Furthermore, we've made slight modifications to messenger.phtml so it aligns with the new API. The primary advantage
  of this update is the ability to dispatch multiple messages concurrently.

  For more information, please refer to [merge request #195](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/195)

- **Fixed multiple checkout session object code smells**

  We have identified methods within the session configuration object that could lead to issues in very specific
  situations. To enhance the developer experience, we've addressed these issues in this release.

  For more information, please refer to [issue #202](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/202)

  Many thanks to Thomas Hägi (Refusion) for the contribution!

- **Upgrade to Magewire 1.10.10**

  The dependency constraint update to version 1.10.9 of Magewire was made to accommodate a novel feature within Magewire,
  enabling the placement of custom Magewire [Hydrators](https://github.com/magewirephp/magewire/blob/main/docs/Hydrators.md) based on order numbers at specific locations within the call stack.

  A dependency constraint update to version 1.10.10 of Magewire was made to leverage the new `Magewire.hasPlugin(pluginName)` method.
  This was used to resolve an issue encountered during the frontend API initialization process, where an error would
  occur due to the absence of the Magewire Loader plugin when it was disabled in the system configuration.
  
  For more information, please refer to [merge request #193](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/193) and [merge request 200](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/200)
  
  Many thanks to Jacob Nguyen (JaJuMa GmbH) for the contribution!

- **Fixed a layout handle code comment typo**

  Fixed a template identifier bug in a comment. The example previously used a slash instead of the double colon syntax.

  For more information, please refer to [issue #190](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/190).

  Many thanks to Christoph Hendreich (In-session) for the contribution!

### Removed

- Nothing removed

## [1.1.6] - 2023-10-06

[1.1.6]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.5...1.1.6

### Added

- **Added IntelliSense compatibility**

  Restructured all PHTML files to be IntelliSense compliant in terms of `@var`, `declare` and `use`.

  For more information, please refer to [issue #178](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/178).

  Many thanks to Jeroen Balk (Adwise) for the contribution!

### Changed

- **Fixed Magento HTML minification**

  Added multiple line ending semicolon to the default messenger PHTML to solve Magento HTML minification errors.

  For more information, please refer to [issue #158](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/158).

  Many thanks to Marcus Venghaus (Freelancer) for the contribution!

- **Fixed selected payment method de-select after a component refresh**

  Resolved an issue in certain scenarios where, following a refresh of the payment method list component,
  the selected payment method was not visually highlighted, resulting in an evaluation failure.

  For more information, please refer to [issue #155](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/155).

  Many thanks to Christoph Hendreich (In-session) and Soham Bhosale (Hummingbird) for the contribution!

### Removed

- Nothing removed

## [1.1.5] - 2023-09-27

[1.1.5]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.4...1.1.5

### Added

- **Allow disabling form modifiers using a null argument item**

  Form modifiers are now filtered to allow for the disabling of existing modifiers within di.xml.

  For more information, please refer to [issue #176](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/176).

  Many thanks to Jeroen Balk (Adwise) for the contribution!

- **Allow street field labels to be configured per store**

  For more information, please refer to [merge request #170](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/170)

  Many thanks to Ronald Bethlehem (Freelancer) for the contribution!

- **Fields now have a `hasAttributesStartingWith` method**

  You can now check if a field possesses an attribute that begins with a specific string.
  This feature can be useful for determining whether a particular setting needs to be configured within a Form Modifier callback.

  For more information, please refer to [issue #173](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/173)

### Changed

- **Removed Checkmo payment method content margin on empty context**

  For more information, please refer to [issue #165](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/165).

- **Fixed exception on empty terms and condition message**

  When the terms and conditions are configured to display a message, but no message is provided, the checkout
  used to raise an exception. This issue has been resolved by requiring the config field and returning
  an empty string for backward compatibility.

  For more information, please refer to [issue #171](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/171).

  Many thanks to Jeroen Balk (Adwise) for the contribution!

- **Upgrade Magewire from 1.10.7 to 1.10.8**

  For more information, please refer to [merge request #184](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/184).

- **Call loader.stop with dummy component to avoid error**

  For more information, please refer to [merge request #180](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/180).

- **Billing address shows email field only for virtual quotes**

  In the past, guest users were presented with an email address form field in the shipping and billing address.  
  This email address was also used as the order contact email. However, this has since changed,
  with the shipping email address now serving as the primary contact email for orders.
  The email field for billing addresses will now only be displayed for quotes containing only virtual items.

  For more information, please refer to [issue #164](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/164)

  Many thanks to Rocky Notten (Wickey) for the contribution!

### Removed

- **Removed an unused `data` argument from the frontend `payment.placeOrder()` method**

  For more information, please refer to [issue #118](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/118).

  Many thanks to Andrzej Wiaderny (Hatimeria) for the contribution!

- **Removed dependency on default hyva-theme styles in favor of default tailwindcss classes**

  This is a backward incompatible change. If you rely on a customized `text-primary` class for the breadcrumb text color,
  be aware that it will change to `text-gray-800` after the upgrade. To revert the change, customize the css in your theme
  to apply `text-primary` again to the selector css `#hyva-checkout-main .breadcrumbs .item`.

  For more information, please refer to [issue #57](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/57).

## [1.1.4] - 2023-08-29

[1.1.4]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.3...1.1.4

### Added

- **Added built-with HTTP response header to hyva checkout routes**

  We've added an `x-built-with: Hyva Checkout` header to Hyvä Checkout pages.

  For more information, please refer to [merge request #164](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/164). 

### Changed

- **Improve default checkout header**

  Previously the checkout used the `checkout` page layout.  
  This was changed to `1column` to provide better styling and improved compatibility with the Hyvä theme out-of-the-box.  
  To remove the top menu, search box, and customer account menu, a new header template was introduced.

  This is a backward-compatibility breaking change. Please refer to the 
  [1.1.4 upgrade notes](https://docs.hyva.io/checkout/hyva-checkout/upgrading/upgrading-to-1.1.4.html) for more
  information on how to upgrade.

  For even more details, please refer to [issue #149](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/149) and
  [merge request #166](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/166).

- **Fix critical error when customer enters percent sign in order comment**

  Previously order comments containing a percent symbol broke rendering in the frontend and admin area.  

  For more information, please refer to [merge request #163](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/163).

  Many thanks to Jeroen Noten (iO) for the contribution!

- **Immediately clear Magewire validation errors on user input**

  Previously the error message remained visible until after the field lost focus. Now the message is hidden as soon as a
  user changes the value.

  For more information, please refer to [issue #162](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/162).

### Removed

- Nothing removed

## [1.1.3] - 2023-08-15

[1.1.3]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.2...1.1.3

### Added

- Nothing added.

### Changed

- **Apply sales rules Free shipping, table-rate etc price adjustments to shipping method on address change**

  Please refer to [issue #131](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/131) for more information.

  Many thanks to Justin van Elst (Publicus) for the contribution!
  
- **Check Country and Region fields are present before applying modifications**

  `Hyva\Checkout\Model\Form\EntityFormModifier\WithRegionModifier` attempted modifications on the region
  field without verifying the presence of both the essential `country_id` and `region` fields on the form. This led to
  issues when the fields were removed. Now they can be removed safely from address forms.

  Please refer to [merge request #157](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/157) for more information.

- **Showing shipping method prices incl. or excl. tax according to configuration**

  Please refer to [issue #133](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/133) for more information.

  Many thanks to Justin van Elst (Publicus) for the contribution!

- **Billing address email not captured when shipping email changed**

  When a guest customer enters their email address and the shipping address is the same as the billing address,
  the email is processed successfully. However, when a customer then modifies the shipping email address,
  the changed email was not set for the billing address.

  Please refer to [issue #140](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/140) for more information.

  Many thanks to Ameer Potrik for the contribution!

- **Form field method `getPreviousValue()` improved**
  
  In the 1.1.2 release, we introduced a new form field feature to get the previous value. This feature is
  handy during form updates to check if a specific field was changed. We've since noticed some flaws in the
  flow, which are now improved. Also, we've added `form:boot` and a `form:{form_namespace}:boot` modification hooks
  to apply changes on both preceding and subsequent form boot.

  Please refer to [merge request #155](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/155) for more information.

- **Select input renderer template now uses `$key` instead of `$option`**

  The template for the form field select input has been enhanced. It now uses the `$key`
  variable for all string-type options. This enhancement provides developers with a more effective
  approach to associating values with options, especially in cases where the `$option` variable is
  dynamic or subject to change.

  Please refer to [merge request #115](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/115) for more information.

### Removed

- Nothing removed.

## [1.1.2] - 2023-08-09

[1.1.2]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.1...1.1.2

### Added

- **New form modification hooks `form:updated` & `form:field:updated`**

  Two new modification hooks empower you to make alterations to forms and their fields if the modification is not
  specific to a single field being updated. Previously this was achieved using distinct field hooks.

  Please refer to [merge request #145](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/145) for more information.

- **Add field method `AbstractEntityField::getPreviousValue()`**

  Performing comparisons to the previous field value using a modification hook proved to be challenging, often resulting in
  situations where an if-statement within a form modification lacked certainty about whether a particular change should be applied.

  Please refer to [merge request #145](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/145) for more information.

### Changed

- **Upgraded Magewire dependency from v1.10.5 to v1.10.7**

  Please refer to [merge request #145](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/145) for more information.

- **Creating typed input fields no longer requires an input data argument**

  When generating a field through form modifiers, it was necessary to include an argument containing an input key with
  the corresponding input type. This approach could be perplexing, given that the method inherently demanded the `$type` to be specified.  
  With this change improvement, the data type is automatically assigned if it is not specified.

  Please refer to [merge request #145](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/145) for more information.

- **Navigating backward via breadcrumbs bugfix**

  The default navigation button allowed backward navigation without validation. However, navigating backward using the 
  breadcrumbs, the checkout process triggered validation, potentially preventing stepping backward to a previous step.
  Now the validation is bypassed when navigating to a previous step.  

  Please refer to [merge request #140](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/140) for more information.

  Many thanks to Marcus Venghaus for the contribution!

- **Moving wire:key from the field to its wrapper element**

  Previously, Magewire faced issues in tracking changes made by form modifiers when the `wire:key` attribute was assigned
  to the innermost element of a field renderer template. To address this, the key is now placed on a wrapper element.  
  By relocating the key, Magewire is able to accurately understand where to perform DOM morphing.
  This change resolves a scenario where a text input was transformed into a select input during subsequent requests.

  Please refer to [merge request #143](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/143) for more information.

- **Form field mutation check bugfix**

  Preiously checking for a difference between the current address values and the updated ones would not work with null values.

  Please refer to [merge request #144](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/144) for more information.

- **Checkout JS API: Fixed a missing `config` object on validation exception**

  Please refer to [merge request #147](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/147) for more information.

- **Checkout JS API: Now works with the Magewire loader plugin**

  The events `loader:start` and `loader:stop` no longer are dispatched to start and stop the loader.  
  Instead, methods on the Magewire loader are called directly. The events are still dispatched, but now from within Magewire.

  Please refer to [merge request #148](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/148) for more information.

### Removed

- Nothing removed.

## [1.1.1] - 2023-07-25

[1.1.1]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.1.0...1.1.1

### Added

- **Form field checkbox type template**

  When creating and adding a form field of type checkbox, the checkbox field rendering view was missing. Now, a 
  dedicated checkbox template is available with the option to also render option relatives.  

  Please refer to [issue #144](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/144) for more information.

  Many thanks to Filipe Bicho (Refusion) for the contribution!

- **Emit event Address Submitted now carrying the save result**

  Previously, you could listen for a `shipping_address_submitted` or `billing_address_submitted` emit event without
  knowing if the save was successful or returned `false`.  
  Now, an emit carries the save result giving developers the option to change course based on it.  

  Please refer to [issue #139](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/139) for more information.

- **Main component redirect template on order place completion**

  There are situations where the Main component is trying to rerender itself despite the quote now being an order.  
  Because of not having the quote object, the component relying on it would possibly break. Therefore, when the PlaceOrderService
  finishes the order with a redirect, the main component will render with a template notifying the customer about the redirect.

  Please refer to [issue #122](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/122) for more information.

  Many thanks to Francesco Marangi (Y1) for the contribution!

- **Form element label renderer**

  Previously each element renderer needed to implement its own hard-coded label HTML creating inconsistency, bad maintainability
  and not being able to swap a label HTML without rewriting a form element renderer. Therefore a label template has been
  introduced used as the default element label renderer.

  Please refer to [issue #142](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/142) for more information.

  Many thanks to Tjitse Efdé (Vendic) for the contribution!

- **Shipping- & Billing street grid renderer (optional)**

  Depending on the street width configuration and checkout developer settings, a new street field renderer will show
  street fields in a two-column grid instead of the default, where ancestor relatives are rendered underneath each other.

  Please refer to [merge request #133](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/133) for more information.

- **Added missing en_US translations**

  Please refer to [issue #117](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/117) for more information.

### Changed

- **Fix PSR-autoload standard issue**

  The class `Hyva\Checkout\Model\Form\EntityField\Primary` has been renamed to
  `Hyva\Checkout\Model\Form\EntityField\PrimaryKey` which matches the file name.

  Please refer to [issue #141](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/141) for more information.

  Many thanks to Gérald CANN (Sdkweb) for the contribution!

- **Improved default styling consistency**

  The default checkout(s) now are closer to the Hyvä Theme styling having a more consistent look and feel over the
  entire store.

  Please refer to [issue #116](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/116) for more information.

  Many thanks to Sean van Zuidam (Siteation) for the contribution!

- **Fix to show a different address book view for billing addresses**

  Previously the billing address book view listened for the shipping address book view setting making it not possible to
  define a different look and feel. This has been fixed and the view now uses its own config setting.

  Please refer to [issue #115](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/115) for more information.

- **Creating a form field without a predefined class**

  Previously creating a form field without a predefined class in di.xml was not possible. Now, a form field can be
  created by giving it a name, some arguments, and an input type resulting in a field using a default implementation of
  `Hyva\Checkout\Model\Form\EntityField\AbstractEntityField`.

  Please refer to [issue #143](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/143) for more information.

  Many thanks to Thijs de Witt (Trinos) for the contribution!

### Removed

- Nothing removed.

## [1.1.0] - 2023-07-10

[1.1.0]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.7...1.1.0

### Added

- **Form modification listener API**

  This new API allows for a wide range of customizations to checkout address forms.  
  It allows callbacks to be triggered when form field values are changed, and modify the form based on it.  

  Please refer to the [form customization documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/form-customization/index.html) for more information.

- **Grouping of form fields**

  A form field can now be assigned as related to another field.  
  This causes the fields to be rendered together, as well as the values to be combined in an array (for example, the street fields use this).

  Please refer to the [form customization documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/form-customization/related-form-elements.html) for more information.

- **New checkout emit messages**

  A couple of new checkout emit messages have been added.

  Please refer to the [form customization documentation](https://docs.hyva.io/checkout/hyva-checkout/devdocs/checkout-emit-messages.html) for more information.

### Changed

- **Fix checkout for new customers with downloadable product or virtual product**

  Previously this resulted in a stack-trace.

  For more information, please refer to [issue #48](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/48).

  Many thanks to Dung La (JaJuMa) for the contribution!

- **Fix unchecking "My billing and shipping address are the same"**

  Previously this caused an address to be rendered as only `,,,,,,,`.

  For more information, please refer to [issue #130](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/130).

- **Require Magewire 1.10.5**

  This change requires the latest version of Magewire.

  For more information, please refer to [merge request #116](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/116).

- **Add referer information to log-in and create an account links**

  This allows customers to be correctly redirected back to the checkout after logging into their account.

  For more information, please refer to [merge request #117](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/117).

- **Fix typo in CSS class**

  In the template `src/view/frontend/templates/breadcrumbs.phtml`, the CSS class `nav-braincrumbs` now is correctly spelled `nav-breadcrumbs`.

  For more information, please refer to [merge request #117](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/117).

### Removed

- **Removed classes, interfaces, methods, and properties**

  A number of classes, interfaces, methods, and properties were removed.  
  However, we do not expect this to impact any customizations of Hyvä Checkout.

  Removed classes:

    - `\Hyva\Checkout\Model\Form\EntityField\Renderer\Regular` (replaced by `\Hyva\Checkout\Model\Form\EntityFormElement\Renderer\Element`)
    - `\Hyva\Checkout\Model\Form\EntityFormFieldsFilterInterface` (no replacement as it was never used)

  The removed properties and methods are not listed here.  
  Please refer to the [backward compatibility breaking changes in 1.1.0 document](https://docs.hyva.io/checkout/hyva-checkout/upgrading/upgrading-to-1.1.0-bc-breaks.html) for details.

## [1.0.7] - 2023-06-19

[1.0.7]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.6...1.0.7

### Added

- Nothing added.

### Changed

- **Require Magewire 1.10.4**

  From this release forward, Hyvä Checkout releases will depend on a specific Magewire version.  
  This will improve reliability and make future upgrades less complex.

  For more information, please refer to [issue #125](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/125)

- **[Bugfix] Add missing resolver argument in di.xml to process hyva_checkout.xml**

  This change fixes a regeression introduced in release 1.0.6.  
  The issue caused additional hyva_checkout.xml files not to be evaluated.

  For more information, please refer to [issue #126](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/126)

- **[Bugfix] Add missing check if nested JS object property is set**

  The missing check could result in an error `Cannot read property of undefined (reading 'id')`.

  For more information, please refer to [issue #123](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/123)

### Removed

- Nothing removed.

## [1.0.6] - 2023-06-09

[1.0.6]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.5...1.0.6

### Added

- **Added Customer Address selection list view**

  The default address selection with grid tiles was not ideal for stores where customers tend to have a lot of records
  in their address book.  
  The list view address selection can be configured for a checkout in the system configuration at *Components >
  Shipping* and *Components > Billing*.

  For more information, please refer to [issue #64](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/64) and the
  referenced merge requests.

### Changed

- **Display shipping totals in checkout with VAT**

  Previously, the shipping totals were missing VAT if configured.

  For more information, please refer to [issue #56](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/56).

### Removed

- Nothing removed.

## [1.0.5] - 2023-06-01

[1.0.5]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.4...1.0.5

### Added

- **Allow saving of billing address to customer address book**

  Previously this was only possible for shipping addresses.

  Please refer to [issue #83](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/83) for more information.

- **"Weee tax" order totals renderer**

  Please refer to [issue #100](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/100) for more information.

- **Option to add a subtitle and SVG logo icon to a Payment method**

  It is now possible to add additional display information to payment methods using layout XML as "metadata".  
  For now, a subtitle and a payment provider logo SVG icon can be added. Additional properties might be added in the future.

  Please refer to [issue #98](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/98) for more information.

- **Navigation button now can have nested elements**

  This feature allows adding DOM elements, for example, SVGs, inside the navigation buttons.

  Please refer to [issue #95](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/95) for more information.

- **Enable the Hyvä Checkout cache by default**

  After running `bin/magento setup:upgrade` for the first time after installing the Hyvä Checkout 1.0.5 or newer, the
  checkout cache will be automatically enabled.

  Please refer to [issue #91](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/91) for more information.

### Changed

- **Fix phrase "No shipping address set." to "No shipping method selected." when no shipping method is selected**

  Please refer to [issue #25](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/25) for more information.

- **Improve Discount Code component default styling**

  We have tried to make the coupon code component less prominent, in other words, less visibly apparent. Both the field
  and the apply button are now responsive to smaller viewports sizes.

  Please refer to [issue #101](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/101) for more information.

- **2- and 3-columns checkout layout breakpoint revision**

  Previously, the 2- and 3-column layout would break on medium-sized screens, but now it has been changed to break on
  large screens instead. This change ensures better visibility on smaller screen sizes, such as mobile or tablet devices.

  Please refer to [issue #101](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/101) for more information.

- **Default email sort order for shipping and billing form fields**

  The email field will now have a default sort order to be rendered as the first field for both the shipping and billing
  address forms.

  Please refer to [issue #109](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/109) for more information.

- **Fixed an issue where it would not render a custom cart item type renderer when it was set via layout xml**

  Please refer to [issue #110](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/110) for more information.

### Removed

- Nothing was removed.

## [1.0.4] - 2023-05-17

[1.0.4]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.3...1.0.4

Release [1.0.1](https://gitlab.hyva.io/hyva-checkout/checkout/-/tree/1.0.1) included a default totals renderer, which
caused a lot of issues. We announced in the Slack support channel that the default totals renderer will be removed
again in the next release. Now every total that needs to be rendered must be declared in layout XML as a child block of
`price-summary.total-segments`. The block alias must match the total code, as in `as="discount"`.

### Added

- **Address & Country change listeners for the payment method list**

  By default, when an address is changed or a country is selected, the payment methods were not refreshed, potentially
  leading to situations where payment methods needed to be displayed or hidden based on changes in the shipping or
  billing address.

  Please refer to [merge request #55](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/55) for more information.

  Many thanks to Francesco Marangi (Y1) for the contribution!

- **Add checkout page type as widget instance target location**

  Please refer to [issue #89](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/89) for more information.

- **Narrowed down the Composer PHP requirement between 7.4 and 8.3**

  Please refer to [issue #88](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/88) for more information.

- **Checkout (Magewire) Component Resolver sort order**

  You now possess the capability to decide whether your custom checkout component resolver should execute before or
  after the core checkout component resolver. Previously, achieving this required utilizing a negative sort order in
  the DI.xml file. However, you can now employ positive numeric values to precisely specify the desired position.

  Please refer to [merge request #74](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/74) for more information.

### Changed

- **Fix scrolling in terms agreement dialog when the content exceeds page height**

  On certain screen sizes, it was not possible to scroll within a terms agreement dialog when it was opened with a
  context that exceeded the height of the screen. As a result, customers were unable to close the dialog since they
  were unable to scroll to the bottom.

  Please refer to [merge request #66](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/66) for more information.

  Many thanks to Mitchel van Kleef (Made by Mouses) for the contribution!

- **Fix for using the undeclared function 'array_first'**

  Please refer to [merge request #93](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/93) for more information.

  Many thanks to Adam Crossland (ZERO-1) for the contribution!

- **Empty Billing Address Form Replaces Pre-Filled Shipping Data**

  Previously, a new guest billing address form would appear with pre-filled shipping address data that could be
  modified. However, it has now been updated so that the billing address form opens empty, without any pre-existing
  information.

  Please refer to [issue #81](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/81) for more information.

### Removed

- **Removed the default total segment renderer (block) and its fallback mechanism**

  In [merge request #44](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/44), a default total segment renderer was implemented to ensure that items are always rendered
  without the need to manually add the necessary block into the Layout XML. However, this led to unintended behavior by
  rendering items that should not be rendered, without providing the option to remove them, as it would default to the
  standard rendering.

  Please refer to [issue #87](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/87) for more information.

## [1.0.3] - 2023-05-09

[1.0.3]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.2...1.0.3

### Added

- Nothing was added.

### Changed

- **Fix checkout layout resolver to only match on checkout route**

  The layout resolver fix introduced in 1.0.2 matches any page on a store with Hyvä Checkout, causing non-checkout pages
  using Magewire to break.

  Please refer to [issue #58](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/85) for more information.

  Many thanks to Marcus Venghaus for the contribution!

### Removed

- Nothing was removed.

## [1.0.2] - 2023-05-02

[1.0.2]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.1...1.0.2

### Added

- Nothing was added.

### Changed

- **Fix unresolvable nested components**

  Components that were dynamically loaded without a page reload or step navigation became unresolvable since the Hyva Checkout component ResolverInterface was unable to comply with its conditions and therefore would fallback on the default Magewire Layout ResolverInterface, which isn’t aware of any additional checkout/step layout handles. Because of this, Blocks couldn’t be found which result in Magewire throwing a 404 - component not found.

  For more information, please refer to [merge request #57](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/57).

  Many thanks to Ivascu Madalin (Buckaroo) & Filipe Bicho (Refusion) for the contribution!

### Removed

- Nothing was removed.

## [1.0.1] - 2023-05-02

[1.0.1]: https://gitlab.hyva.io/hyva-checkout/checkout/-/compare/1.0.0...1.0.1

### Added

- **Compatibility with PHP 8.2**

  For more information, please refer to [issue #35](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/35).

- **Default totals renderer**

  Previously, custom totals where not rendered, unless they where explicitly added as a child to the `price-summary.total-segments` block with the total code as the block alias.  
  Now, if there is no child matching the total modal, the default renderer is used rendering the total code and value.

  For more information, please refer to [issue #37](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/37).

  Many thanks to Marcus Venghaus for the contribution!

### Changed

- **Fix shipping total rendering**

  Previouly the shipping total was not shown because the block was missing the `as="shipping"` attribute in the layout xml, so it was rendered using the default total renderer instead.

  For more information, please refer to [merge request #48](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/48).

  Many thanks to Thijs de Witt (Trinos) for the contribution!

- **Changed the checkout cache ID from checkout to hyva_checkout**

  This low impact change was made to avoid potential cache key name collisions with other extensions or possible future Magento core changes.

  For more information, please refer to [merge request #48](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/48).

- **Change type of Phone number field from "text" to "tel"**

  This change can improve the keyboard layout on mobile devices when entering a value.

- **Session expiration Alert popup message**

  When a customer session expires while idling for a long time during checkout, a informative message is displayed rather than an error message.

- **Fix validation error for newly registered customers without address in address book**

  Previously, customers who had just created an account and tried to checkout where unable to place an order because the billing address was not properly initialized on the quote.

  For more information, please refer to [issue #55](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/55).

  Many thanks to Jacek Lopuszynski for the contribution!

- **Fix coupon code component being incorrectly disabled**

  For more information, please refer to [issue #50](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/50).

  Many thanks to Marcus Venghaus for the contribution!

- **Update README to refer to the correct composer package names**

  Previously old package names used during development that are no longer valid where referenced in the README file.

  For more information, please refer to [issue #43](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/43).

- **When a customer address is deleted, the address ID is removed from the quote**

  Previously an exception was thrown when the address ID on the quote referenced a non-existing customer address.

  For more information, please refer to [issue #39](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/39).

  Many thanks to Marcus Venghaus for the contribution!

- **Update the price summary after changing the payment method**

  Some payment methods like cash on delivery have an effect on the price summary.

  For more information, please refer to [issue #36](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/36).

  Many thanks to Marcus Venghaus for the contribution!

- **Fix possible bypass of required form fields**

  For more information, please refer to [issue #24](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/24) and [merge request #49](https://gitlab.hyva.io/hyva-checkout/checkout/-/merge_requests/49).

- **Fix issue with Magewire loading spinner remaining visible after session timeout**

  For more information, please refer to [issue #5](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/5).

- **Handle session corruption because of cookie domain conflicts more gracefully**

  In the rare situation, where the main store is running on a second level domain (e.g. test.com),  
  and the staging environment was running on a subdomain of the same domain (e.g. staging.test.com),  
  the main store cookie conflicted with the subdomain cookie, resulting in lost sessions.  
  This caused a problem where the checkout was trying to collect Magewire components based on data of the main domain when on the subdomain.

  For more information, please refer to [issue #42](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/42).

- **Select inputs like COUNTRY_ID will now wait to update when a user starts typing**

  For more information, please refer to [issue #34](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/34).

- **Magewire error alert overlay is now hidden in production mode**

  For more information, please refer to [issue #21](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/21).

- **Make system configuration for shipping and billing form management more user-friendly**

  For more information, please refer to [issue #7](https://gitlab.hyva.io/hyva-checkout/checkout/-/issues/7).

- **The default checkout login step now uses the 1column "layout"**

### Removed

- Nothing was removed.

## 1.0.0 - 2023-02-24

### Added

- Initial Release

### Changed

- Nothing was changed.

### Removed

- Nothing was removed.
