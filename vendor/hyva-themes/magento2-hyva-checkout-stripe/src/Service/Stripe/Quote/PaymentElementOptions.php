<?php
/*
 * Hyvä Themes - https://hyva.io
 *  Copyright © Hyvä Themes 2020-present. All rights reserved.
 *  This product is licensed per Magento install
 *  See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutStripe\Service\Stripe\Quote;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PaymentElementOptions
{
    private Session $checkoutSession;

    public function __construct(
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function get(array $params): array
    {
        $params['defaultValues']['billingDetails'] = $this->getDetails();
        $params['fields'] = $this->getFields();

        // I'm not sure why this is required, but Stripe gives warnings if these are included.
        unset(
            $params['locale'],
            $params['apiKey'],
            $params['appInfo'],
            $params['options'],
            $params['successUrl'],
            $params['cvcIcon'],
            $params['isOrderPlaced']
        );

        return $params;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function getDetails(): array
    {
        $cart = $this->checkoutSession->getQuote();
        $billingAddress = $cart->getBillingAddress();

        $name = implode(' ', array_filter([
            $billingAddress->getFirstname(),
            $billingAddress->getMiddlename(),
            $billingAddress->getLastname()
        ]));

        return [
            'name' => $name,
            'email' => $billingAddress->getEmail(),
            'phone' => $billingAddress->getTelephone(),
            'address' => [
                'line1' => $billingAddress->getStreet()[0],
                'line2' => $billingAddress->getStreet()[1] ?? '',
                'city' => $billingAddress->getCity(),
                'state' => $billingAddress->getRegion() ?? '',
                'country' => $billingAddress->getCountryId(),
                'postal_code' => $billingAddress->getPostcode(),
            ]
        ];
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function getFields(): array
    {
        $billingAddress = $this->checkoutSession->getQuote()->getBillingAddress();
        $hasState = $billingAddress->getRegion() || $billingAddress->getRegionCode() || $billingAddress->getRegionId();

        $line1 = $billingAddress->getStreet()[0];
        $line2 = $billingAddress->getStreet()[1] ?? false;

        return [
            'billingDetails' => [
                'name' => 'never',
                'email' => 'never',
                'phone' => $billingAddress->getTelephone() ? 'never' : 'auto',
                'address' => [
                    'line1' => $line1 ? 'never' : 'auto',
                    'line2' => $line2 ? 'never' : 'auto',
                    'city' => $billingAddress->getCity() ? 'never' : 'auto',
                    'state' => $hasState ? 'never' : 'auto',
                    'country' => $billingAddress->getCountryId() ? 'never' : 'auto',
                    'postalCode' => $billingAddress->getPostcode() ? 'never' : 'auto',
                ]
            ]
        ];
    }
}
