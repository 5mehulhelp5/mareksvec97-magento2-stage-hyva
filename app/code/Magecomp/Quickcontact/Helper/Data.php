<?php
namespace Magecomp\Quickcontact\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $storeManagerInterface;

    const QUICKCONTACT_IS_ENABLED = 'quickcontact/general/enable';

    public function __construct(
		\Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $StoreManagerInterface

    ) {
    	parent::__construct($context);
        $this->storeManagerInterface = $StoreManagerInterface;
	}

     public function getStoreid()
    {
        return $this->storeManagerInterface->getStore()->getId();
    } 

    public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,$storeId
        );
    }

}