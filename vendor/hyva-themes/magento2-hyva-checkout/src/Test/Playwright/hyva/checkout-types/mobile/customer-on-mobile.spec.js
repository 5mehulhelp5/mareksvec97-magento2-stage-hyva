// @ts-check
import {account, addressDE, addressUS, uniquifyEmail} from "../../fixtures/customer-data"

const {test, expect} = require('@playwright/test')
import {HyvaCheckoutMobile} from '../../page-objects/hyva-checkout-mobile'
import {SimpleProductPage} from "../../page-objects/simple-product-page"
import {CustomerAccount} from "../../page-objects/customer-account"
import {HyvaCheckout} from "../../page-objects/hyva-checkout"

// test.use({
//     viewport: { width: 414, height: 896 }, // iPhone 11 portrait mode
// })

const email = uniquifyEmail(account.email)

test.beforeAll(async ({browser}) => {
  const page = await browser.newPage()
  const customerAccount = await CustomerAccount.registerAccount(page, email, account.password, addressUS.firstname, addressUS.lastname)
  await customerAccount.createCustomerAddress(addressUS)
  await customerAccount.logout()
  await page.close()
})

test.describe('[mobile] A logged in customer', () => {

  test.describe.configure({mode: 'serial'});

  test.beforeEach(async ({page}) => {
    await CustomerAccount.login(page, email, account.password)
    const productPage = await SimpleProductPage.open(page)
    await productPage.addToCart()
  });

  test('can not proceed to payment without a shipping method', async ({page}) => {
    const checkout = await HyvaCheckoutMobile.open(page, 'mobile')
    await checkout.assertOnStep('Shipping Address')
    await expect(checkout.getShippingMethodOption('Fixed')).not.toBeChecked() // flatrate
    await expect(checkout.getShippingMethodOption('Table Rate')).not.toBeChecked() // bestway
    await checkout.tryNextStep()
    await checkout.waitUntilIdle(500) // wait for validation to trigger and process
    await checkout.assertOnStep('Shipping Address')
    await checkout.assertComponentError(HyvaCheckoutMobile.selectors.shippingMethodComponent, 'The shipping method is missing.')
  })

  test('can check out with "Fixed" shipping method', async ({page}) => {
    const checkout = await HyvaCheckoutMobile.open(page, 'mobile')
    await checkout.assertOnStep('Shipping Address')
    await checkout.selectShippingMethod('Fixed')
    await checkout.goToNextStep('Payment')

    // ensure backwards/forwards navigation works
    await checkout.goToPrevStep('Shipping Address')
    await checkout.goToNextStep('Payment')

    await checkout.selectPaymentMethod('Check / Money order') // checkmo
    await checkout.goToNextStep('Review Your Order')
    await checkout.placeOrder()
    await expect(page.getByRole('heading').getByText('Thank you for your purchase!')).toBeVisible()
  })

  test('can change selected shipping method if selection becomes unavailable after shipping address change', async ({page}) => {
    const checkout = await HyvaCheckoutMobile.open(page, 'mobile')
    await checkout.assertOnStep('Shipping Address')

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

    await checkout.goToNextStep('Payment')
  })

  test('can edit an addresses, cancel, and edit again and cancel again', async ({page}) => {
    const checkout = await HyvaCheckoutMobile.open(page, 'mobile')
    await checkout.assertOnStep('Shipping Address')

    await checkout.addNewShippingAddress(addressDE)

    const newAddressForm = checkout.getComponentLocator(HyvaCheckout.selectors.newShippingAddressComponent)

    await checkout.getAddressGridEditButtons().first().click()

    await expect(newAddressForm).toBeVisible()
    await expect(newAddressForm.getByLabel('First Name')).toHaveValue(addressDE.firstname)
    await newAddressForm.getByRole('button', {name: 'Cancel'}).click()
    await expect(newAddressForm).not.toBeVisible()

    await checkout.getAddressGridEditButtons().first().click()

    await expect(newAddressForm).toBeVisible()
    await expect(newAddressForm.getByLabel('First Name')).toHaveValue(addressDE.firstname)

    await newAddressForm.getByRole('button', {name: 'Cancel'}).click()
    await expect(newAddressForm).not.toBeVisible()
  })
})
