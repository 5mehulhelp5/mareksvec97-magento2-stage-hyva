<?php

namespace BigConnect\Cookies\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper
{
    const BASE_CONFIG_XML_PREFIX            = 'bigconnect_cookies/general/%s';


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

    /* COOKIES START */

    public function getCookie($configField)
    {
        return $this->scopeConfig->getValue(
            sprintf(self::BASE_CONFIG_XML_PREFIX, $configField),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
  

     /* COOKIES END */

}