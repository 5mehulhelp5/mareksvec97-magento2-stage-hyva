# Metalio Hyvä theme audit

## Findings

1. **Config access via ObjectManager in the trust bar partial** – `trustbar.phtml` instantiates the global ObjectManager to pull store config values. That bypasses Magento dependency injection, makes the template harder to test, and prevents environment-specific overrides or caching from working consistently. Consider creating a lightweight view model that injects `ScopeConfigInterface` and exposes the same values to the template.
2. **Broken translation string for the projects counter** – the same template concatenates `__('completed", "projects')`, which renders the literal text instead of a proper translatable phrase (it currently outputs `completed", "projects`). The string should be fixed (for example `__('completed projects')`) so translators and copy can work as expected.
3. **Demo imagery ships in production checkout** – four placeholder gallery images are bundled and preloaded on every cart page (`Magento_Checkout::images/2.jpg` … `5.jpg`). If they are only for showcase purposes, consider replacing them with lightweight project thumbnails stored in the CMS or gating the block behind a feature flag to avoid unnecessary payload and content mismatch.

## Recommended next steps

- Replace the inline ObjectManager usage with a view model that fetches review/project metadata via DI and injects it into the template.
- Correct the translation helper call so the project count tagline is readable and translatable.
- Move the gallery assets to real project images (or lazy-load them) to avoid shipping placeholder media to shoppers.
