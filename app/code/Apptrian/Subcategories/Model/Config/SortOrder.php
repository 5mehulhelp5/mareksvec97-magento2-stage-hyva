<?php
/**
 * @category  Apptrian
 * @package   Apptrian_Subcategories
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\Subcategories\Model\Config;

use Magento\Framework\Exception\LocalizedException;

class SortOrder extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate and prepare data before saving config value.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        
        $pattern = '/^[0-2,]+$/';
        $validator = preg_match($pattern, $value);
        
        if (!$validator) {
            $message = __(
                'Please correct subcategory data sort order: "%1".',
                $value
            );
            throw new LocalizedException($message);
        }
        
        return $this;
    }
}
