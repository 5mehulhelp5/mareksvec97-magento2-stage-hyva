<?php
/**
 * @category  Apptrian
 * @package   Apptrian_PinterestPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\PinterestPixel\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;

class PixelId implements MessageInterface
{
    /**
     * Unique system message identity.
     */
    const MESSAGE_IDENTITY = 'apptrian_pinterestpixel_system_notification_pixel_id';
    
    /**
     * @var \Apptrian\PinterestPixel\Helper\Data
     */
    public $helper;

    /**
     * Constructor.
     *
     * @param \Apptrian\PinterestPixel\Helper\Data $helper
     */
    public function __construct(
        \Apptrian\PinterestPixel\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    
    /**
     * Retrieve unique system message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether the system message should be shown
     *
     * @return bool
     */
    public function isDisplayed()
    {
        $config = '';
        $display = true;
       
        $stores = $this->helper->storeManager->getStores();
       
        foreach ($stores as $store) {
            $config = $this->helper->getConfig(
                'apptrian_pinterestpixel/general/pixel_id',
                $store->getId()
            );
            
            if (!empty($config) && $config != '123456789012345') {
                $display = false;
                break;
            }
        }
       
        return $display;
    }

    /**
     * Retrieve system message text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getText()
    {
        return __(
            'Please provide your Pinterest Pixel ID. 
            Stores > 
            Configuration > 
            Apptrian Extensions > 
            Pinterest Pixel > 
            General > 
            Pinterest Pixel ID'
        );
    }

    /**
     * Retrieve system message severity
     * Possible default system message types:
     * - MessageInterface::SEVERITY_CRITICAL
     * - MessageInterface::SEVERITY_MAJOR
     * - MessageInterface::SEVERITY_MINOR
     * - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_NOTICE;
    }
}
