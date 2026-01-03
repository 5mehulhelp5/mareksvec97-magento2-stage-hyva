<?php

namespace N1site\CategoryImage\Model\Category;

/**
 * Class DataProvider
 * @package N1site\CategoryImage\Model\Category
 */

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @return array
     */
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'image2';

        return $fields;
    }
}