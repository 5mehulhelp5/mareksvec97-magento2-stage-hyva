// @ts-check
import {account, addressDE, addressUS, uniquifyEmail} from "../../fixtures/customer-data"

const {test, expect} = require('@playwright/test')
import {SimpleProductPage} from "../../page-objects/simple-product-page"
import {CustomerAccount} from "../../page-objects/customer-account"
import {HyvaCheckout} from "../../page-objects/hyva-checkout"

const email = uniquifyEmail(account.email)

test.beforeAll(async ({browser}) => {
  const page = await browser.newPage()
  const customerAccount = await CustomerAccount.registerAccount(page, email, account.password, addressUS.firstname, addressUS.lastname)
  await customerAccount.createCustomerAddress(addressUS)
  await customerAccount.logout()
  await page.close()
})

test.describe('[onepage] A logged in customer', () => {

  test.describe.configure({mode: 'serial'});

  test.beforeEach(async ({page}) => {
    await CustomerAccount.login(page, email, account.password)
    const productPage = await SimpleProductPage.open(page)
    await productPage.addToCart()
  });

  test('can check out with "Fixed" shipping method', async ({page}) => {
    const checkout = await HyvaCheckout.open(page, 'onepage')

    await expect(page.getByRole('heading').getByText('Shipping Address')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Billing Address')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Shipping Methods')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Payment Method')).toBeVisible()
    await expect(page.getByRole('heading').getByText('Order Summary')).toBeVisible()

    await checkout.selectShippingMethod('Fixed')
    await checkout.selectPaymentMethod('Check / Money order') // checkmo
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
    await checkout.addNewShippingAddress(addressDE)

    // the new address should be checked after entering
    await expect(checkout.getAddressGridOptionContaining(`${addressDE.firstname} ${addressDE.lastname}`)).toBeChecked()
    await expect(checkout.getShippingMethodOption('Table Rate')).toHaveCount(0)
    await expect(checkout.getShippingMethodOption('Fixed')).toHaveCount(1)
    await expect(checkout.getShippingMethodOption('Fixed')).not.toBeChecked()

    await checkout.selectShippingMethod('Fixed')
    await checkout.getAddressGridOptionContaining(`${addressUS.firstname} ${addressUS.lastname}`).check()

    await expect(checkout.getShippingMethodOption('Table Rate')).toHaveCount(1)
    await expect(checkout.getShippingMethodOption('Fixed')).toHaveCount(1)
    await expect(checkout.getShippingMethodOption('Fixed')).toBeChecked()

    // the new address still should be present in the address list, even after the first address is selected again
    //await expect(checkout.getAddressGridOptionContaining(`${addressDE.firstname} ${addressDE.lastname}`)).toHaveCount(1)

    await checkout.selectShippingMethod('Table Rate')
    await checkout.selectPaymentMethod('Check / Money order') // checkmo
    await checkout.placeOrder()

    await expect(page.getByRole('heading').getByText('Thank you for your purchase!')).toBeVisible()
  })
})
