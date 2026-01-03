<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

// phpcs:disable Generic.Metrics.NestingLevel.TooHigh

namespace Hyva\Checkout\Model\Config;

class Structure extends \Magento\Config\Model\Config\Structure
{
    public function getSectionList()
    {
        /**
         * extended to add grandChild level for admin sections so it withstands reload of the page without closing subsubsections
         */
        if (empty($this->sectionList)) {
            foreach ($this->_data['sections'] as $sectionId => $section) {
                if (array_key_exists('children', $section) && is_array($section['children'])) {
                    foreach ($section['children'] as $childId => $child) {
                        $sectionKey = $sectionId . '_' . $childId;
                        $this->sectionList[$sectionKey] = true;
                        if (array_key_exists('children', $child) && is_array($child['children'])) {
                            foreach ($child['children'] as $grandChildId => $grandChild) {
                                $this->sectionList[$sectionKey . '_' . $grandChildId] = true;
                            }
                        }
                    }
                }
            }
        }
        return $this->sectionList;
    }
}
