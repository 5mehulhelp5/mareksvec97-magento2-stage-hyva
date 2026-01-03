<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use MagePal\GoogleAnalytics4\Model\Session as EnhancedEcommerceSession;

class JsDataLayer implements SectionSourceInterface
{
    /**
     * @var EnhancedEcommerceSession
     */
    protected $enhancedEcommerceSession;

    /**
     * @param EnhancedEcommerceSession $enhancedEcommerceSession
     */
    public function __construct(
        EnhancedEcommerceSession $enhancedEcommerceSession
    ) {
        $this->enhancedEcommerceSession = $enhancedEcommerceSession;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getSectionData()
    {
        return [
            'cart_items' => $this->enhancedEcommerceSession->getProductDataObjectArray()
        ];
    }
}
