<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Plugin\CustomerData;

use MagePal\GoogleAnalytics4\Model\Session as GoogleAnalytics4Session;
use MagePal\EnhancedEcommerce\CustomerData\JsDataLayer;

class JsDataLayerPlugin
{

    /**
     * @var GoogleAnalytics4Session
     */
    protected $googleAnalytics4Session;

    /**
     * @param GoogleAnalytics4Session $googleAnalytics4Session
     */
    public function __construct(
        GoogleAnalytics4Session $googleAnalytics4Session
    ) {
        $this->googleAnalytics4Session = $googleAnalytics4Session;
    }

    /**
     * @param JsDataLayer $subject
     * @param $result
     */
    public function afterGetSectionData(JsDataLayer $subject, $result)
    {
        $data = $this->googleAnalytics4Session->getProductDataObjectArray();

        if (!empty($data)) {
            $result['cart_items'] = $data;
        }

        return $result;
    }
}
