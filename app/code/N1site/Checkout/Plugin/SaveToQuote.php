<?php
namespace N1site\Checkout\Plugin;

use Magento\Quote\Model\QuoteRepository;

class SaveToQuote {
	
    protected $quoteRepository;
    private $customerSession;
    private $customerFactory;

    public function __construct(
		QuoteRepository $quoteRepository,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory
	) {
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
    }

    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        if (!$extAttributes = $addressInformation->getExtensionAttributes()) {
            return;
        }

        $quote = $this->quoteRepository->getActive($cartId);
		
		// file_put_contents(__DIR__.'/test.txt', $extAttributes->getPnId());
		// exit;
		
		$customer = $this->customerSession->getCustomer();
		
		if ($customer->getData()) {
			
			// file_put_contents(__DIR__.'/customer.txt', print_r($customer->getData(), 1));
			
			if ($extAttributes->getPnId() || $extAttributes->getTaxId()) {
				// if ($customer->getPnId()) $customer->setCustomAttribute('pn_id', $customer->getPnId());
				// if ($customer->getTaxId()) $customer->setCustomAttribute('tax_id', $customer->getTaxId());

				$customerData = $customer->getDataModel();		
				if ($extAttributes->getPnId()) $customerData->setCustomAttribute('pn_id', $extAttributes->getPnId());
				if ($extAttributes->getTaxId()) $customerData->setCustomAttribute('tax_id', $extAttributes->getTaxId());
				$customer->updateData($customerData);
				$customerResource = $this->customerFactory->create();
				if ($extAttributes->getPnId()) $customerResource->saveAttribute($customer, 'pn_id');
				if ($extAttributes->getTaxId()) $customerResource->saveAttribute($customer, 'tax_id');

				// file_put_contents(__DIR__.'/customer.txt', print_r($this->customerSession->getCustomer()->debug(), 1));
			}
		}
		
		if ($extAttributes->getPnId()) {
			$this->customerSession->setPnId($extAttributes->getPnId());
		}
		if ($extAttributes->getTaxId()) {
			$this->customerSession->setTaxId($extAttributes->getTaxId());
		}

		// $quote->setPnId($extAttributes->getPnId());
		// $quote->setTaxId($extAttributes->getTaxId());

    }
}
