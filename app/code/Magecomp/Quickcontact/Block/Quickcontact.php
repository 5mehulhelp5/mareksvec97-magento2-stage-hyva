<?php

namespace Magecomp\Quickcontact\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magecomp\Quickcontact\Helper\Data;

class Quickcontact extends Template
{ 
    protected $context;
    protected $helperdata;

    public function __construct(
        Context $context,
        Data $helperdata,
        array $data = [] )
    {
        $this->helperdata = $helperdata;
        parent::__construct($context, $data);
    }
    
    public function getSubmitUrl()
    {
        return $this->getUrl('quickcontact/index/save',['_secure' => true]);
    }

    public function isEnable()
    {
        return $this->helperdata->getConfig('quickcontact/general/enable');
    }

    public function getBgcolor()
    {
        return $this->helperdata->getConfig('quickcontact/general/bgcolor');
    }
}