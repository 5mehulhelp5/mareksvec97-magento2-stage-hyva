<?php
/*
 * Hyvä Themes - https://hyva.io
 *  Copyright © Hyvä Themes 2020-present. All rights reserved.
 *  This product is licensed per Magento install
 *  See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutStripe\Observer\SalesQuotePaymentImportDataBefore;

use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class SetAdditionalData implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        /** @var PaymentInterface $payment */
        $payment = $observer->getData('payment');

        /** @var DataObject $input */
        $input = $observer->getData('input');

        /** @var array $additionalData */
        $additionalData = $input->getData('additional_data');

        $additionalData['payment_method'] = $payment->getAdditionalInformation('payment_method');
        $additionalData['payment_element'] = $payment->getAdditionalInformation('payment_element');
        $additionalData['manual_authentication'] = $payment->getAdditionalInformation('manual_authentication');

        if ($payment->getAdditionalInformation('cvc_token')) {
            $additionalData['cvc_token'] = $payment->getAdditionalInformation('cvc_token');
        }

        $input->setData('additional_data', $additionalData);
    }
}
