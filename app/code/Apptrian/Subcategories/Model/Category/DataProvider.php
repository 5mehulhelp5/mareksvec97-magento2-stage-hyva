<?php
/**
 * @category  Apptrian
 * @package   Apptrian_Subcategories
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */

namespace Apptrian\Subcategories\Model\Category;

/**
 * Add thumbnail via data provider.
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * Add thumbnail to the map.
     *
     * @return array
     */
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'apptrian_thumbnail';
        
        return $fields;
    }
}
