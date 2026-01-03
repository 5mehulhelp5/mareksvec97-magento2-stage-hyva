// @ts-check
import {expect} from "@playwright/test";

export class SimpleProductPage {
  constructor(page) {
    this.page = page
  }

  static async open(page) {
    const productPage = new SimpleProductPage(page)
    await page.goto('/strive-shoulder-pack.html')

    return productPage
  }

  async addToCart() {
    await expect(this.page.getByText('In stock')).toBeVisible()
    await this.page.getByTitle('Add to Cart', {exact: true}).click()
    await expect(this.page.getByText('You added Strive Shoulder Pack to your shopping cart.')).toBeVisible()
  }
}
