<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Helper;

/** Enhanced Success Page Data Helper */

class Esp extends Data
{

    /**
     * @return string
     */
    public function getUpsellItemListName()
    {
        return $this->getConfigValue('enhanced_success_page/upsell/item_list_name');
    }

    /**
     * @return string
     */
    public function getUpsellClassName()
    {
        return $this->getConfigValue('enhanced_success_page/upsell/class_name');
    }

    /**
     * @return string
     */
    public function getUpsellContainerClass()
    {
        return $this->getConfigValue('enhanced_success_page/upsell/container_class');
    }

    /**
     * @return string
     */
    public function getRelatedItemListName()
    {
        return $this->getConfigValue('enhanced_success_page/related/item_list_name');
    }

    /**
     * @return string
     */
    public function getRelatedClassName()
    {
        return $this->getConfigValue('enhanced_success_page/related/class_name');
    }

    /**
     * @return string
     */
    public function getRelatedContainerClass()
    {
        return $this->getConfigValue('enhanced_success_page/related/container_class');
    }

    /**
     * @return string
     */
    public function getCrosssellItemListName()
    {
        return $this->getConfigValue('enhanced_success_page/crosssell/item_list_name');
    }

    /**
     * @return string
     */
    public function getCrosssellClassName()
    {
        return $this->getConfigValue('enhanced_success_page/crosssell/class_name');
    }

    /**
     * @return string
     */
    public function getCrosssellContainerClass()
    {
        return $this->getConfigValue('enhanced_success_page/crosssell/container_class');
    }

    /**
     * @return string
     */
    public function getRecentViewedItemListName()
    {
        return $this->getConfigValue('enhanced_success_page/recent_viewed/item_list_name');
    }

    /**
     * @return string
     */
    public function getRecentViewedClassName()
    {
        return $this->getConfigValue('enhanced_success_page/recent_viewed/class_name');
    }

    /**
     * @return string
     */
    public function getRecentViewedContainerClass()
    {
        return $this->getConfigValue('enhanced_success_page/recent_viewed/container_class');
    }
}
