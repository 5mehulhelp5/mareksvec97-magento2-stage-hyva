<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Data;

class Home extends CatalogWidget
{
    public function addImpressionList()
    {
        $this->setImpressionList(
            $this->getItemListName(),
            $this->_eeHelper->getHomeWidgetClassName(),
            $this->_eeHelper->getHomeWidgetContainerClass()
        );
    }

    protected function _init()
    {
        $this->setItemListName($this->_eeHelper->getHomeWidgetItemListName());
        $this->setUseWidgetTitle($this->_eeHelper->getHomeWidgetUseWidgetTitle());
        return $this;
    }
}
