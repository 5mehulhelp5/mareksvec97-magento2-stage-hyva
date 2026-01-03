<?php

namespace Magecomp\Quickcontact\Block\Adminhtml\Quickcontact;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\UrlInterface;
use Magecomp\Quickcontact\Model\QuickcontactFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Request\Http;

class View extends Template
{
    protected $context;
    protected $_urlBuilder;
    protected $_quickcontactFactory;
    protected $objectManager;
    protected $request;

    public function __construct(
        Context $context,
        QuickcontactFactory $quickcontactFactory,
        Http $request,
        ObjectManagerInterface $objectManager,
        UrlInterface $urlBuilder
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->_quickcontactFactory = $quickcontactFactory;
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    public function getBackURL()
    {
        return $this->_urlBuilder->getUrl('quickcontact/quickcontact/index', ['_secure' => true]);
    }

    public function getContactInfo()
    {
        return $this->_quickcontactFactory->create()->load($this->request->getParam('id'));
    }

    public function getMediaUrl()
    {
        $media_dir = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
    
    public function getFileUrl($name)
    {
        return $this->getMediaUrl() . 'magecomp/quickcontact/' .$name;
    }
}