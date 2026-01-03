<?php
/*
 * Hyvä Themes - https://hyva.io
 *  Copyright © Hyvä Themes 2020-present. All rights reserved.
 *  This product is licensed per Magento install
 *  See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutStripe\Magewire\Customer;

use Magento\Framework\Serialize\SerializerInterface;
use Magewirephp\Magewire\Component;
use StripeIntegration\Payments\Api\ServiceInterface;
use StripeIntegration\Payments\Helper\Generic;
use StripeIntegration\Payments\Helper\InitParams;

class PaymentMethods extends Component
{
    private SerializerInterface $serializer;
    private ServiceInterface $service;

    private Generic $helper;
    private InitParams $stripeInitParams;

    public array $initParams = [];
    public array $paymentMethods = [];
    public bool $paymentMethodsLoaded = false;

    public function __construct(
        SerializerInterface $serializer,
        ServiceInterface $service,
        Generic $helper,
        InitParams $stripeInitParams
    ) {
        $this->serializer = $serializer;
        $this->service = $service;
        $this->helper = $helper;
        $this->stripeInitParams = $stripeInitParams;
    }

    public function initStripe(): void
    {
        $this->paymentMethodsLoaded = true;
        $this->listPaymentMethods();

        try
        {
            $customer = $this->helper->getCustomerModel();

            if (!$customer->existsInStripe()) {
                $customer->createStripeCustomerIfNotExists();
            }

            $this->initParams = $this->serializer->unserialize(
                $this->stripeInitParams->getMyPaymentMethodsParams($customer->getStripeId())
            );
        }
        catch (\Exception $e)
        {
            // Silence is golden
        }
    }

    public function listPaymentMethods(): void
    {
        $result = $this->service->list_payment_methods();

        $this->paymentMethods = $this->serializer->unserialize($result);
    }

    public function savePaymentMethod(string $paymentMethodId): void
    {
        $result = $this->serializer->unserialize($this->service->add_payment_method($paymentMethodId));

        if (array_key_exists('client_secret', $result)) {
            $this->dispatchBrowserEvent('stripe-authenticate-customer', [
                'clientSecret' => $result['client_secret']
            ]);
        }

        $this->listPaymentMethods();
    }

    public function deletePaymentMethod(string $paymentMethodId): void
    {
        $this->service->delete_payment_method($paymentMethodId);

        $this->listPaymentMethods();
    }
}
