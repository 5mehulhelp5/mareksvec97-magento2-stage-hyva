// @ts-check
import {addressDE, addressUS} from "../../fixtures/customer-data";
import {HyvaCheckout} from '../../page-objects/hyva-checkout'
import {SimpleProductPage} from "../../page-objects/simple-product-page"

const {test, expect} = require('@playwright/test')

test.beforeEach(async ({page}) => {
  const productPage = await SimpleProductPage.open(page)
  await productPage.addToCart()
});


test.describe('[onepage] A guest', () => {
  test('can check out with "Table Rate" shipping method', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'onepage')

    await expect(page.getByRole('heading').getByText('Shipping Address')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Billing Address')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Shipping Methods')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Payment Method')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Order Summary')).toBeVisible()

    await checkout.useShippingAddress(addressUS, addressUS.email)
    await checkout.selectShippingMethod('Table Rate')
    await checkout.useShippingAddressAsBillingAddress()
    await checkout.selectPaymentMethod('Check / Money order')
    await checkout.placeOrder()

    await expect(page.getByRole('heading').getByText('Thank you for your purchase!')).toBeVisible()
  })

  test('can change selected shipping method if selection becomes unavailable after shipping address change', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'onepage')

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
    await checkout.selectPaymentMethod('Check / Money order')
    await checkout.placeOrder()

    await expect(page.getByRole('heading').getByText('Thank you for your purchase!')).toBeVisible()
  })
})
