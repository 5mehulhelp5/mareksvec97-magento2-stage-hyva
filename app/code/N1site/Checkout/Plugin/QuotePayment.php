<?php

namespace N1site\Checkout\Plugin;

class QuotePayment {
    /**
     * @param \Magento\Quote\Model\Quote\Payment $subject
     * @param array $data
     * @return array
     */
    public function beforeImportData(\Magento\Quote\Model\Quote\Payment $subject, array $data) {
		
		$data['additional_information']['test'] = 111;
		$data['additional_data']['test'] = 222;
		
        // if (array_key_exists('additional_information', $data)) {
            $subject->setAdditionalInformation($data['additional_information']);
        // }
		
            $subject->setAdditionalData($data['additional_data']);

        return [$data];
    }
}