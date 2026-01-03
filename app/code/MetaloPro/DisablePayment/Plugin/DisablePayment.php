<?php

namespace MetaloPro\DisablePayment\Plugin;

use Magento\Payment\Model\Checks\CanUseForCountry;

class DisablePayment
{
    public function afterIsApplicable(
        CanUseForCountry $subject,
        $result,
        $paymentMethod,
        $quote
    ) {
        // Získajte aktuálny Store View a jeho Locale
        $store = $quote->getStore();
        $locale = $store->getConfig('general/locale/code'); // Získa locale (napr. sk_SK, cs_CZ)

        // Zoznam Locale, kde chcete povoliť Dobierku
        $allowedLocales = ['sk_SK','hr_HR','cs_CZ','sl_SI'];

        // Overte platobnú metódu a Locale
        if (
            $paymentMethod->getCode() === 'cashondelivery' && 
            !in_array($locale, $allowedLocales)
        ) {
            return false; // Zakáže platobnú metódu
        }

        return $result;
    }
}
