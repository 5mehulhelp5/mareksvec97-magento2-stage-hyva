// @ts-check
import {expect} from "@playwright/test";
import {HyvaCheckout} from "./hyva-checkout"

export class HyvaCheckoutMobile extends HyvaCheckout {

  static async open(page, checkoutType, viewPort) {
    const checkout = new HyvaCheckoutMobile(page)
    const type = checkoutType || 'mobile'

    await page.setViewportSize({width: 414, height: 896, ...(viewPort || {})}) // iPhone 11 portrait mode)

    await page.goto(`/checkout?checkout=${type}`);
    await page.locator(`body.checkout-${type}`)

    return checkout
  }

  async assertOnStep(expected) {
    await expect(this.page.getByRole('heading').getByText(expected)).toBeVisible()
  }
}
