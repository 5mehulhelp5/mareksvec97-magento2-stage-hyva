// @ts-check
import {expect} from "@playwright/test";

export class HyvaCheckout {
    constructor(page) {
        this.page = page
    }

    static selectors = {
        nextButton: 'nav button[rel=next]',
        prevButton: 'nav button[rel=prev]',
        guestEmailComponent: 'checkout.guest-details',
        shippingAddressComponent: 'checkout.shipping-details.address-form',
        shippingMethodComponent: 'checkout.shipping.methods',
        newShippingAddressComponent: 'checkout.shipping-details.address-list.form'
    }

    static async open(page, checkoutType) {
        const checkout = new HyvaCheckout(page)
        const type = checkoutType || 'default'

        await page.goto(`/checkout?checkout=${type}`);
        await page.locator(`body.checkout-${type}`)

        return checkout
    }

    async assertOnStep(expected) {
        await expect(this.page.getByRole('button', {name: expected})).toBeVisible()
    }

    async getSelectedOptionLabel(selectLocator) {
        return await selectLocator.evaluate(async (select) => {
            return select.selectedOptions.length ? select.selectedOptions[0].label : false
        })
    }

    async selectCountry(componentName, countrySelectLocator, country) {
        if (await this.getSelectedOptionLabel(countrySelectLocator) !== country) {
            await this.waitForMagewire(componentName, async () => countrySelectLocator.selectOption(country))
        }
    }

    getComponentLocator(componentName) {
        return this.page.locator(`[wire\\:id="${componentName}"]`)
    }

    async enterAddress(componentName, address) {
        const addressForm = this.getComponentLocator(componentName)
        await this.selectCountry(componentName, addressForm.getByRole('combobox', {name: /Country/}), address.country)
        await addressForm.getByRole('combobox', {name: /State\/Province/}).selectOption(address.region)
        await addressForm.getByLabel('First Name').type(address.firstname)
        await addressForm.getByLabel('Last Name').type(address.lastname)
        await addressForm.getByLabel('Street Address').type(address.street)
        await addressForm.getByLabel('City').type(address.city)
        await addressForm.getByLabel('Zip/Postal Code').type(address.postcode)
        await addressForm.getByLabel('Phone Number').type(address.telephone)
        await this.page.waitForTimeout(100) // this is required, otherwise the last field value isn't submitted
    }

    async enterShippingAddress(address, email) {
        if (email) {
            await this.getComponentLocator(HyvaCheckout.selectors.guestEmailComponent).getByLabel('Email address').type(email)
        }
        await this.enterAddress(HyvaCheckout.selectors.shippingAddressComponent, address)
    }

    async useShippingAddress(address, email) {
        await this.waitForMagewire(HyvaCheckout.selectors.shippingAddressComponent, () => this.enterShippingAddress(address, email))
    }

    getNewAddressButton() {
        return this.page.getByRole('button', {name: /New Address/i})
    }

    async addNewShippingAddress(address) {
        const formComponentName = HyvaCheckout.selectors.newShippingAddressComponent;
        await this.waitForMagewire(
            formComponentName,
            async () => await this.getNewAddressButton().click()
        )
        await this.enterAddress(formComponentName, address)
        await this.waitForMagewire(
            formComponentName,
            async () => this.getComponentLocator(formComponentName).getByRole('button', {name: 'Save'}).click()
        )
    }

    getAddressGridOptionContaining(text) {
        return this.page.locator(`.address-grid .address-item`).filter({hasText: text}).getByRole('radio')
    }

    getAddressGridOptions() {
        return this.page.locator(`.address-grid .address-item`).getByRole('radio')
    }

    getAddressGridEditButtons() {
        return this.page.locator(`.address-grid .address-item`).getByRole('button', {name: 'Edit Address'})
    }

    async getShippingAddressAsBillingAddress() {
        return this.page.getByLabel('My billing and shipping address are the same')
    }

    async useShippingAddressAsBillingAddress() {
        const checkbox = await this.getShippingAddressAsBillingAddress()
        if (!await checkbox.isChecked()) {
            await this.waitForMagewire(
                'checkout.billing-details',
                async () => (await this.getShippingAddressAsBillingAddress()).check()
            )
        }
    }

    async useDistinctBillingAddress() {
        const checkbox = await this.getShippingAddressAsBillingAddress()
        if (await checkbox.isChecked()) {
            await this.waitForMagewire(
                'checkout.billing-details',
                async () => (await this.getShippingAddressAsBillingAddress()).uncheck()
            )
        }
    }

    getShippingMethodOption(shippingOptionLabel) {
        return this.page.getByLabel(new RegExp(shippingOptionLabel))
    }

    async selectShippingMethod(shippingOptionLabel) {
        await this.waitForMagewire(
            'checkout.shipping.methods',
            async () => this.getShippingMethodOption(shippingOptionLabel).check()
        )
    }

    getPaymentMethodOption(paymentOptionLabel) {
        return this.page.getByLabel(new RegExp(paymentOptionLabel))
    }

    async selectPaymentMethod(paymentOptionLabel) {
        await this.waitForMagewire('checkout.payment.methods', async () => this.getPaymentMethodOption(paymentOptionLabel).check())
    }

    async assertNextButtonDisabled() {
        await expect(this.page.locator(HyvaCheckout.selectors.nextButton)).toBeDisabled()
    }

    async assertNextButtonEnabled() {
        await expect(this.page.locator(HyvaCheckout.selectors.nextButton)).toBeEnabled()
    }

    async assertPrevButtonDisabled() {
        await expect(this.page.locator(HyvaCheckout.selectors.prevButton)).toBeDisabled()
    }

    async assertPrevButtonEnabled() {
        await expect(this.page.locator(HyvaCheckout.selectors.prevButton)).toBeEnabled()
    }

    async goToNextStep(nextStepLabel) {
        await this.waitForMagewire('hyva-checkout-main', async () => this.tryNextStep())
        nextStepLabel && (await this.assertOnStep(nextStepLabel))
    }

    async goToPrevStep(prevStepLabel) {
        await this.waitForMagewire('hyva-checkout-main', async () => this.tryPrevStep())
        prevStepLabel && (await this.assertOnStep(prevStepLabel))
    }

    async tryNextStep() {
        await this.assertNextButtonEnabled()
        await this.page.locator(HyvaCheckout.selectors.nextButton).click()
    }

    async tryPrevStep() {
        await this.assertPrevButtonEnabled()
        await this.page.locator(HyvaCheckout.selectors.prevButton).click()
    }

    getPlaceOrderButton() {
        return this.page.getByRole('button', {name: /Place Order/})
    }

    async assertPlaceOrderDisabled() {
        await expect(this.getPlaceOrderButton()).toBeDisabled()
    }

    async assertPlaceOrderEnabled() {
        await expect(this.getPlaceOrderButton()).toBeEnabled()
    }

    async placeOrder() {
        await this.assertPlaceOrderEnabled();
        await this.getPlaceOrderButton().click()
        await this.page.waitForTimeout(500) // wait for Magewire to start process the click and start the request
    }

    async assertComponentError(componentName, expected) {
        await expect(this.page.getByTestId(`${componentName}-message`).getByText(expected)).toHaveCount(1)
    }

    async assertFormValidationError(fieldLocator, expected) {
        const id = await fieldLocator.getAttribute('aria-errormessage')
        await expect(this.page.locator(`#${id}`)).toHaveText(expected)
    }

    async waitUntilIdle(ms) {
        await this.page.waitForTimeout(ms || 20) // wait for Magewire to start requests
        await this.page.waitForFunction(() => hyvaCheckout.isIdle.now)
    }

    /*
     * Wait for a response to a magewire route to complete while executing a callback.
     *
     * If this fails, it probably is a timeout during a Magwire request for ${componentName} to complete while executing ${action.toString()}
     *
     * The failure reason message will be something like
     *     waiting for response "hyva-checkout-main"
     * But the timeout actually happens because the action locator took too long to become available.
     *
     * Set debugOutput = true at the beginning of the function to log verbose output while running the test.
     */
    async waitForMagewire(componentName, action) {
        const debugOutput = false

        // don't use the full **/magewire/post/livewire/message/ route so the component name isn't truncated in error messages
        const responsePromise = this.page.waitForResponse(`**/${componentName}`)

        debugOutput && console.log(`waiting for \`**/${componentName}\`\n while running \`${action.toString()}\``)
        debugOutput && await this.page.evaluate(s => console.log(s), `waiting for \`**/${componentName}\`\nwhile running  \`${action.toString()}\``)

        await action()

        debugOutput && console.log(`done executing \`${action.toString()}\``)
        debugOutput && await this.page.evaluate(s => console.log(s), `done executing \`${action.toString()}\``)

        await responsePromise

        debugOutput && console.log(`completed request \`**/${componentName}\``)
        debugOutput && await this.page.evaluate(s => console.log(s), `completed request \`**/${componentName}\``)

        await this.waitUntilIdle() // wait for Magewire to process the response and complete requests triggered by emits
    }
}
