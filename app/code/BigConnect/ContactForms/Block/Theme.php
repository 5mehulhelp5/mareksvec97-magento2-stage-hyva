<?php

namespace BigConnect\ContactForms\Block;

use BigConnect\ContactForms\Helper\Config as ContactFormsConfig;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ContactForms extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var ThemeConfig
     */
    protected $configHelper;

    /**
     * @param Context         $context
     * @param ThemeConfig $configHelper
     */
    public function __construct(
        Context $context,
        ContactFormsConfig $configHelper
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
    }

    public function getGeneralSettings($type) {
        $active = $this->configHelper->getConfigParam($type);
        if ($active == 1) {
           return "ahoj";     
        }
        return false;
    }


}