<?php
/**
 * @category  Apptrian
 * @package   Apptrian_PinterestPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */

namespace Apptrian\PinterestPixel\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class MatchingSection implements SectionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;
    
    /**
     * @var \Apptrian\PinterestPixel\Helper\Data
     */
    public $helper;
    
    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Apptrian\PinterestPixel\Helper\Data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Apptrian\PinterestPixel\Helper\Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->helper = $helper;
    }
    
    public function getSectionData()
    {
        $customerData = [];
        $customerId = $this->customerSession->getCustomerId();
        
        if (!$customerId) {
            $customerId = 0;
        }
        
        $customerData = $this->helper->getUserDataForJs($customerId);
        
        return [
            'matching_data' => $customerData,
        ];
    }
}
