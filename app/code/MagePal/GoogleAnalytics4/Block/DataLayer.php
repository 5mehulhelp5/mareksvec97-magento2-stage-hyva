<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use MagePal\GoogleAnalytics4\Helper\Data;
use MagePal\GoogleTagManager\Block\DataLayerAbstract;
use MagePal\GoogleTagManager\Helper\Data as GtmHelper;

class DataLayer extends DataLayerAbstract
{

    /**
     * @var string
     */
    protected $dataLayerEventName = 'magepal_ee_datalayer';

    /**
     * @var string
     */
    protected $_template = 'MagePal_GoogleAnalytics4::data_layer.phtml';

    /**
     * @var Data
     */
    protected $_eeHelper;

    /**
     * @var array $_impressionList
     s*/
    protected $_impressionList = [];

    /**
     * DataLayer constructor.
     * @param Context $context
     * @param GtmHelper $gtmHelper
     * @param Data $eeHelper
     * @param array $data
     * @throws NoSuchEntityException
     */
    public function __construct(
        Context $context,
        GtmHelper $gtmHelper,
        Data $eeHelper,
        array $data = []
    ) {
        $this->_eeHelper = $eeHelper;
        parent::__construct($context, $gtmHelper, $data);
    }

    /**
     * @return array
     */
    public function getImpressionList()
    {
        return (array) $this->_impressionList;
    }

    /**
     * @return string
     */
    public function getImpressionListJson()
    {
        return json_encode(!empty($this->getImpressionList()) ? $this->getImpressionList() : []);
    }

    /**
     * @return bool
     */
    public function hasImpressionList()
    {
        return !empty($this->getImpressionList()) || !empty($this->processDataLayer());
    }

    /**
     * @return string
     */
    public function getItemListName()
    {
        return trim($this->getData('item_list_name'));
    }

    /**
     * @return string
     */
    public function getItemListId()
    {
        return strtolower(preg_replace("/[^a-zA-Z]+/", '_', $this->getItemListName()));
    }

    /**
     * @param string $itemListName
     * @param string $className
     * @param string $containerClass
     * @return DataLayer
     */
    public function setImpressionList($itemListName, $className, $containerClass)
    {
        $this->_impressionList[] = [
            'item_list_name' => $itemListName,
            'item_list_id' => $this->getItemListId(),
            'class_name' => $className,
            'container_class' => $containerClass
        ];

        return $this;
    }

    /**
     * @return $this
     */
    protected function _init()
    {
        return $this;
    }

    /**
     * Render tag manager script
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_eeHelper->isEnabled()) {
            return '';
        }

        if ($this->_eeHelper->isGa4Enabled()) {
            $this->_dataLayer();
        }

        return parent::_toHtml();
    }

    /**
     * Add category data to datalayer
     * @return $this
     */
    protected function _dataLayer()
    {
        return $this;
    }
}
