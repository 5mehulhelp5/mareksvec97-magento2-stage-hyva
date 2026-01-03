<?php
/**
 * Magehq
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the mageqh.com license that is
 * available through the world-wide-web at this URL:
 * https://magehq.com/license.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Magehq
 * @package    Magehq_CustomOrderNumber
 * @copyright  Copyright (c) 2022 Magehq (https://magehq.com/)
 * @license    https://magehq.com/license.html
 */
 
namespace Magehq\CustomOrderNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoSaveBeforeObserver extends AbstractObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->updateIncrementId($observer->getCreditmemo(), self::TYPE_CREDITMEMO);
    }

    /**
     * @param $object \Magento\Sales\Model\Order
     * @return \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection
     */
    public function getCollectionForOrder($object)
    {
        return $object->getCreditmemosCollection();
    }
}
