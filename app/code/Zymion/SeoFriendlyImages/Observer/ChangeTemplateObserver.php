<?php

namespace Zymion\SeoFriendlyImages\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Use the correct template on the frontend.
 */
class ChangeTemplateObserver implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Init
     *
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Set the correct template to be used
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->moduleManager->isEnabled('Magento_ProductVideo')) {
            $observer->getBlock()->setTemplate('Zymion_SeoFriendlyImages::helper/gallery_video.phtml');
        } else {
            $observer->getBlock()->setTemplate('Zymion_SeoFriendlyImages::helper/gallery.phtml');
        }
    }
}
