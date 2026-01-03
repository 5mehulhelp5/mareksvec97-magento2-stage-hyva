<?php

declare(strict_types=1);

namespace Hyva\CheckoutStripe\Plugin\Stripe\Response;

// Import the original class to be plugged into and the core Magento Checkout Session.
use StripeIntegration\Payments\Api\Response\ECEResponse;
use Magento\Checkout\Model\Session as CheckoutSession;

class ECEResponsePlugin
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;


    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Before plugin for getLimitedShippingRates
     *
     * @param ECEResponse $subject
     * @param array $rates
     * @param int $limit
     * @return array
     */
    public function beforeGetLimitedShippingRates(
        ECEResponse $subject,
        array $rates,
        $limit = 9
    ) {
        // Manipulate $rates here before the original method is called

        $selectedMethod = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingMethod();
        //fails if theres no method set
        if ($selectedMethod) {
            $newRates = [];
            foreach ($rates as $rate) {
                if (isset($rate['id']) && $rate['id'] == $selectedMethod) {
                    array_unshift($newRates, $rate); // Move selected to the top
                } else {
                    $newRates[] = $rate;
                }
            }
            $rates = $newRates;
            $rates = array_values($rates);
        }

        return [$rates, $limit];
    }

}