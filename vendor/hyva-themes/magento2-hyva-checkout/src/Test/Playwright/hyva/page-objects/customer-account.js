// @ts-check
import {expect} from "@playwright/test";

export class CustomerAccount {
  constructor(page) {
    this.page = page;
  }

  static async registerAccount(page, email, password, firstname, lastname) {
    await page.goto('/customer/account/create')
    await expect(page.getByRole('heading').getByText('Create New Customer Account')).toBeVisible()

    await page.getByLabel('First Name').type(firstname)
    await page.getByLabel('Last Name').type(lastname);
    await page.getByLabel('Email', {exact: true}).type(email)
    await page.getByRole('textbox', {name: 'Password', exact: true}).type(password);
    await page.getByLabel('Confirm Password').type(password)
    await page.getByRole('button', {name: 'Create an Account'}).click()

    await expect(page.getByText('Thank you for registering')).toBeVisible()

    return new CustomerAccount(page)
  }

  async createCustomerAddress(address) {
    await this.page.goto('/customer/address/new');
    await expect(this.page.getByText('Add New Address')).toBeVisible()

    await this.page.getByLabel('Phone Number').type(address.telephone)
    await this.page.getByLabel('Street Address', {exact: true}).type(address.street)
    await this.page.getByLabel('Zip/Postal Code').type(address.postcode)
    await this.page.getByLabel('City').type(address.city)
    await this.page.getByRole('combobox', {name: 'Country'}).selectOption(address.country);
    await this.page.waitForTimeout(500)
    await this.page.getByRole('combobox', {name: 'State/Province'}).selectOption(address.region);
    // set it twice, since once doesn't work with Alpine v2 in the CI pipeline (it passes locally though)
    await this.page.getByRole('combobox', {name: 'State/Province'}).selectOption(address.region);
    await this.page.getByRole('button', {name: 'Save Address'}).click();
    await expect(this.page.getByText('You saved the address.')).toBeVisible()
  }

  static async login(page, email, password) {
    await page.goto('/customer/account/')
    await expect(page.getByText('Customer Login')).toBeVisible()
    await page.getByLabel('Email', {exact: true}).type(email)
    await page.getByRole('textbox', {name: 'Password'}).type(password);
    await page.getByRole('button', {name: 'Sign In'}).click()
    await expect(page.getByRole('heading').getByText('My Account')).toBeVisible()


    return new CustomerAccount(page)
  }

  async logout() {
    await this.page.goto('/customer/account/logout')
  }
}
