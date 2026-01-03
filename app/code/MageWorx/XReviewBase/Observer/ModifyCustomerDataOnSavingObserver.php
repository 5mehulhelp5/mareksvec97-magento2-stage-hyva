<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Observer;

use Magento\Framework\Event\ObserverInterface;
use MageWorx\GeoIP\Model\Geoip;
use MageWorx\XReviewBase\Model\Review\AdditionalDetailsFields\Config as FieldsConfig;

class ModifyCustomerDataOnSavingObserver implements ObserverInterface
{
    /**
     * @var Geoip
     */
    protected $geoIp;

    /**
     * @var FieldsConfig
     */
    protected $fieldsConfig;

    /**
     * @var \MageWorx\XReviewBase\Model\VerifiedBuyerDetector
     */
    protected $verifiedBuyerDetector;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * ModifyCustomerDataOnSavingObserver constructor.
     *
     * @param Geoip $geoIp
     * @param \MageWorx\XReviewBase\Model\VerifiedBuyerDetector $verifiedBuyerDetector
     * @param FieldsConfig $fieldsConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $_fileUploaderFactory
     */
    public function __construct(
        \MageWorx\GeoIP\Model\Geoip $geoIp,
        \MageWorx\XReviewBase\Model\VerifiedBuyerDetector $verifiedBuyerDetector,
        FieldsConfig $fieldsConfig,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->geoIp                 = $geoIp;
        $this->fieldsConfig          = $fieldsConfig;
        $this->verifiedBuyerDetector = $verifiedBuyerDetector;
        $this->customerSession       = $customerSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Review\Controller\Product\Post $controllerAction */
        $controllerAction = $observer->getEvent()->getControllerAction();

        $request   = $controllerAction->getRequest();
        $productId = (int)$request->getParam('id');

        if (!$request->getPostValue() || !$productId) {
            return;
        }

        foreach ($this->fieldsConfig->getFrontendDiscardFieldsForReviewDetail() as $fieldName) {
            $request->setPostValue($fieldName, null);
        }

        $data = $this->geoIp->getCurrentLocation();

        $request->setPostValue('location', $data['code'] ?? null);
        $request->setPostValue('region', $data['region'] ?? null);

        if ($this->customerSession->isLoggedIn()) {
            if ($this->verifiedBuyerDetector->execute($this->customerSession->getCustomerId(), $productId)) {
                $request->setPostValue('is_verified', '1');
            }
        }
    }
}
