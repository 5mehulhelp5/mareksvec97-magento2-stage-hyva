<?php

namespace BigConnect\Cookies\Block;

use BigConnect\Cookies\Helper\Config as CookiesConfig;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Cookies extends \Magento\Framework\View\Element\Template
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
        CookiesConfig $configHelper
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
    }

    public function getCookiesEnable() {
        return $this->configHelper->getCookie('active');
    }

    public function getCountry() {
        return $this->configHelper->getCookie('country');
    }

    public function getName() {
        return $this->configHelper->getCookie('name');
    }

    public function getPrivacyPolicyUrl() {
        return $this->configHelper->getCookie('privacypolicyurl');
    }


}