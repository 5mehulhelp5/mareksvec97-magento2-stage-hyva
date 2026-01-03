<?php
/**
 * @category  Apptrian
 * @package   Apptrian_PinterestPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */

namespace Apptrian\PinterestPixel\Block;

class Code extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Apptrian\PinterestPixel\Helper\Data
     */
    public $helper;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Apptrian\PinterestPixel\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Apptrian\PinterestPixel\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        
        parent::__construct($context, $data);
    }
    
    /**
     * Used in .phtml file and returns array of data.
     *
     * @return array
     */
    public function getPinterestPixelData()
    {
        $data = [];
        
        $data['id_data']               = $this->helper->getPinterestPixelId();
        $data['full_action_name']      = $this->getRequest()->getFullActionName();
        $data['page_handles']          = $this->helper->getPageHandles();
        $data['page_handles_category'] = $this->helper->getPageHandles('category');
        $data['page_handles_product']  = $this->helper->getPageHandles('product');
        $data['page_handles_quote']    = $this->helper->getPageHandles('quote');
        $data['page_handles_order']    = $this->helper->getPageHandles('order');
        $data['page_handles_search']   = $this->helper->getPageHandles('search');
    
        return $data;
    }
    
    /**
     * Returns configuration value for Pinterest Pixel.
     *
     * @return bool
     */
    public function isPixelEnabled()
    {
        return $this->helper->isPixelEnabled();
    }
    
    /**
     * Returns configuration value for base_code_enabled.
     *
     * @return bool
     */
    public function isBaseCodeEnabled()
    {
        return $this->helper->isBaseCodeEnabled();
    }
    
    /**
     * Returns configuration value for noscript_enabled.
     *
     * @return bool
     */
    public function isNoScriptEnabled()
    {
        return $this->helper->isNoScriptEnabled();
    }
    
    /**
     * Returns configuration value for page with all.
     *
     * @return int
     */
    public function isPageWithAll()
    {
        return $this->helper->isPageWithAll();
    }
    
    /**
     * Returns category data needed for tracking.
     *
     * @return array
     */
    public function getCategoryData()
    {
        return $this->helper->getCategoryDataForJs();
    }
    
    /**
     * Returns product data needed for tracking.
     *
     * @return array
     */
    public function getProductData($id = 0)
    {
        return $this->helper->getProductData($id);
    }
    
    /**
     * Returns data needed for tracking from order object.
     *
     * @return array
     */
    public function getOrderData()
    {
        return $this->helper->getOrderData();
    }
    
    /**
     * Returns data needed for tracking from quote object.
     *
     * @return array
     */
    public function getQuoteData()
    {
        return $this->helper->getQuoteData();
    }
    
    /**
     * Returns search data needed for tracking.
     *
     * @return array
     */
    public function getSearchData()
    {
        return $this->helper->getSearchDataForJs();
    }
    
    /**
     * Returns configuration value for event.
     *
     * @return bool
     */
    public function isEventEnabled($event)
    {
        return $this->helper->isEventEnabled($event);
    }
    
    /**
     * Returns configuration value for detect_selected_sku
     *
     * @return bool
     */
    public function isDetectSelectedSkuEnabled($productType, $server = false)
    {
        return $this->helper->isDetectSelectedSkuEnabled($productType, $server);
    }
    
    /**
     * Returns price decimal sign
     *
     * @return string
     */
    public function getPriceDecimalSymbol()
    {
        return $this->helper->getPriceDecimalSymbol();
    }
    
    /**
     * Returns flag based on "Stores > Cofiguration > Sales > Tax
     * > Price Display Settings > Display Product Prices In Catalog"
     * Returns 0 or 1 instead of 1, 2, 3.
     *
     * @return int
     */
    public function getDisplayTaxFlag()
    {
        return $this->helper->getDisplayTaxFlag();
    }
    
    /**
     * Returns data for registration event.
     *
     * @param int $customerId
     * @return array
     */
    public function getDataForRegistrationEvent()
    {
        return $this->helper->getDataForRegistrationEvent();
    }
    
    /**
     * Returns configuration value for fp_cookie.
     *
     * @return boolean
     */
    public function isFpCookie()
    {
        return $this->helper->isFpCookie();
    }
    
    /**
     * Returns configuration value for md_frequency.
     *
     * @return boolean
     */
    public function isMdFrequency()
    {
        return $this->helper->isMdFrequency();
    }
}
