<?php

namespace BigConnect\ContactForms\Block;

use BigConnect\ContactForms\Helper\Config as ContactFormsConfig;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Information;

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

    protected $scopeConfig;


    /**
     * @param Context         $context
     * @param ThemeConfig $configHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ContactFormsConfig $configHelper
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->configHelper = $configHelper;
    }

    public function getGeneralSettings($type) {
        return $this->configHelper->getConfigParam($type);
    }

    public function getAvatar() {
        $image = $this->configHelper->getConfigParam('avatar');
        $link = "/pub/media/contactform/avatars/";
        return $link.$image;
    }

    public function getNumberEmail() {
        return $this->configHelper->getConfigParam('show_number_or_email');
    }

    public function getLink() {
        return $this->configHelper->getConfigParam('link');
    }

    public function getBlockTitle() {
        return $this->configHelper->getProductParam('title');
    }

    public function getBlockMessage() {
        return $this->configHelper->getProductParam('message');
    }

    public function getBlockCategoryTitle() {
        return $this->configHelper->getCategoryParam('title');
    }

    public function getBlockCategoryMessage() {
        return $this->configHelper->getCategoryParam('message');
    }

    public function getBlockCategoryTime() {
        return $this->configHelper->getCategoryParam('time');
    }

    public function getStorePhone(){
    return $this->scopeConfig->getValue(
        'general/store_information/phone',
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }



}