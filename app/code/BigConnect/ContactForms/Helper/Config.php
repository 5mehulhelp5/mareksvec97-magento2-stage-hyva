<?php

namespace BigConnect\ContactForms\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper
{
    const BASE_CONFIG_XML_PREFIX           = 'bigconnect_contactforms/general/%s';
    const BASE_CONFIG_XML_PREFIX_PRODUCT   = 'bigconnect_contactforms/contactforms_product/%s';
    const BASE_CONFIG_XML_PREFIX_CATEGORY  = 'bigconnect_contactforms/contactforms_category/%s';



    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Return configuration param from module admin settings
     *
     * @param string $configField
     * @return mixed
     */
    public function getConfigParam($configField)
    {
        return $this->scopeConfig->getValue(
            sprintf(self::BASE_CONFIG_XML_PREFIX, $configField),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getProductParam($configField)
    {
        return $this->scopeConfig->getValue(
            sprintf(self::BASE_CONFIG_XML_PREFIX_PRODUCT, $configField),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCategoryParam($configField)
    {
        return $this->scopeConfig->getValue(
            sprintf(self::BASE_CONFIG_XML_PREFIX_CATEGORY, $configField),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


}