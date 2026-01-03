// @ts-check
import {addressDE, addressUS} from "../../fixtures/customer-data";

const {test, expect} = require('@playwright/test')
import {HyvaCheckout} from '../../page-objects/hyva-checkout'
import {SimpleProductPage} from "../../page-objects/simple-product-page"

test.beforeEach(async ({page}) => {
  const productPage = await SimpleProductPage.open(page)
  await productPage.addToCart()
});


test.describe('[default] A guest', () => {
  test('does not allow stepping and shows shipping address form validation errors', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'default')
    await checkout.assertOnStep('Shipping')
    await checkout.selectShippingMethod('Fixed')
    await checkout.tryNextStep()

    await checkout.assertOnStep('Shipping')
    const addressForm = checkout.getComponentLocator(HyvaCheckout.selectors.shippingAddressComponent)
    await expect(addressForm.locator('input[name=firstname]:invalid')).toHaveCount(1)
    await expect(addressForm.getByLabel('First Name')).toHaveAttribute('aria-invalid', 'true')
    await checkout.assertFormValidationError(addressForm.getByLabel('First Name'), 'This is a required field.')

    await checkout.useShippingAddress(addressUS, addressUS.email)
    await page.waitForTimeout(250)
    await checkout.goToNextStep('Review & Payments')
    await checkout.useDistinctBillingAddress()
    await checkout.useShippingAddressAsBillingAddress()
    await checkout.goToPrevStep('Shipping')
    await addressForm.getByLabel('Phone Number').fill('')
    await checkout.tryNextStep()
    await checkout.assertOnStep('Shipping')
    await checkout.assertFormValidationError(addressForm.getByLabel('Phone Number'), 'This is a required field.')
  })

  test('can not proceed to payment without a shipping method', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'default')
    await checkout.assertOnStep('Shipping')
    await checkout.useShippingAddress(addressUS, addressUS.email)

    await expect(checkout.getShippingMethodOption('Fixed')).not.toBeChecked() // flatrate
    await expect(checkout.getShippingMethodOption('Table Rate')).not.toBeChecked() // bestway

    await checkout.tryNextStep()
    await checkout.waitUntilIdle(500)

    await checkout.assertOnStep('Shipping')
    await checkout.assertComponentError(HyvaCheckout.selectors.shippingMethodComponent, 'The shipping method is missing.')
  })

  test('can check out with "Fixed" shipping method', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'default')
    await checkout.assertOnStep('Shipping')

    await checkout.useShippingAddress(addressUS, addressUS.email)
    await checkout.selectShippingMethod('Fixed')
    await checkout.goToNextStep('Review & Payments')
    await checkout.useShippingAddressAsBillingAddress()
    await checkout.selectPaymentMethod('Purchase Order') // purchaseorder
    await checkout.waitForMagewire('checkout.payment.method.purchaseorder', async () => page.getByLabel('Purchase Order Number').type('test-po'))

    await checkout.placeOrder()

    await expect(page.getByRole('heading').getByText('Thank you for your purchase!')).toBeVisible()
  })

  test('can check out with "Table Rate" shipping method', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'default')
    await checkout.assertOnStep('Shipping')
    await checkout.useShippingAddress(addressUS, addressUS.email)
    await checkout.selectShippingMethod('Table Rate')
    await checkout.goToNextStep('Review & Payments')
    await checkout.useShippingAddressAsBillingAddress()

    // ensure navigation works
    await checkout.goToPrevStep('Shipping')
    await checkout.goToNextStep('Review & Payments')

    await checkout.selectPaymentMethod('Check / Money order')
    await checkout.placeOrder()

    await expect(page.getByRole('heading').getByText('Thank you for your purchase!')).toBeVisible()
  })

  test('can change selected shipping method if selection becomes unavailable after shipping address change', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'default')
    await checkout.assertOnStep('Shipping')

    // by default all shipping methods are availables
    await expect(checkout.getShippingMethodOption('Table Rate')).toHaveCount(1)
    await expect(checkout.getShippingMethodOption('Fixed')).toHaveCount(1)

    await checkout.selectShippingMethod('Table Rate') // select tablerates
    // no tablerates are defined for Germany
    await checkout.useShippingAddress(addressDE, addressDE.email)

    await expect(checkout.getShippingMethodOption('Table Rate')).toHaveCount(0)
    await expect(checkout.getShippingMethodOption('Fixed')).toHaveCount(1)
    await expect(checkout.getShippingMethodOption('Fixed')).not.toBeChecked()

    await checkout.selectShippingMethod('Fixed')
    await checkout.goToNextStep('Review & Payments')
  })
})
