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

use Magento\Sales\Model\EntityInterface;

abstract class AbstractObserver
{
    const TYPE_INVOICE = 'invoice';
    const TYPE_SHIPMENT = 'shipment';
    const TYPE_CREDITMEMO = 'creditmemo';

    /**
     * @var \Magehq\CustomOrderNumber\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magehq\CustomOrderNumber\Helper\Module
     */
    protected $incrementIdGenerator;

    /**
     * @var \Magento\SalesSequence\Model\Manager
     */
    protected $sequenceManager;

    /**
     * AbstractObserver constructor.
     *
     * @param \Magehq\CustomOrderNumber\Helper\Config $configHelper
     * @param \Magehq\CustomOrderNumber\Helper\Generator $incrementIdGenerator
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     */
    public function __construct(
        \Magehq\CustomOrderNumber\Helper\Config $configHelper,
        \Magehq\CustomOrderNumber\Helper\Generator $incrementIdGenerator,
        \Magento\SalesSequence\Model\Manager $sequenceManager
    ) {
        $this->configHelper = $configHelper;
        $this->incrementIdGenerator = $incrementIdGenerator;
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * @param $object
     * @return mixed
     */
    abstract public function getCollectionForOrder($object);

    /**
     * If "use same number as order number" is used, set the order increment_id for object
     *
     * @param $object
     * @param $entityType
     * @return $this
     */
    protected function updateIncrementId($object, $entityType)
    {
        if (!$this->configHelper->isModuleEnabled()) {
            return $this;
        }

        if (!$object->getId()) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $object->getOrder();
            $storeId = $order->getStoreId();

            // Is (order/invoice/...) number customizer enabled for this store ID?
            if (!$this->configHelper->getConfigFlag($entityType, 'enabled', $storeId)) {
                return $this;
            }

            // Shall the order number be used? Just for invoice/shipment/credit memo numbers
            if (!$this->configHelper->getConfigFlag($entityType, 'same_as_order', $storeId)) {
                // Generate new increment ID
                if ($object instanceof EntityInterface && $object->getIncrementId() == null) {
                    $originalSequence = $this->sequenceManager->getSequence(
                        $object->getEntityType(),
                        $object->getStore()->getGroup()->getDefaultStoreId()
                    )->getNextValue();
                    $incrementId = $this->incrementIdGenerator->generateIncrementId($object, $entityType, $originalSequence);
                    $object->setIncrementId($incrementId);
                }
                // Then, return, as otherwise the object ID would be set to the same ID as the order number, which is done in the following code
                return $this;
            }

            $orderIncrementId = $order->getIncrementId();
            $numberPrefix = $this->configHelper->getConfigValue($entityType, 'id_prefix', $storeId);
            $replaceInId = $this->configHelper->getConfigValue($entityType, 'replace_in_id', $storeId);
            if (!empty($replaceInId)) {
                $orderIncrementId = str_replace($replaceInId, "", $orderIncrementId);
            }
            if (empty($orderIncrementId)) {
                return $this;
            }

            // Get invoice/shipment/credit memo collection
            $collection = $this->getCollectionForOrder($order);

            $maxIterations = 99;
            $newIncrementId = false;
            $subIncrementIdCounter = 0;
            while ($newIncrementId === false) {
                if ($subIncrementIdCounter > $maxIterations) {
                    break;
                }
                if ($subIncrementIdCounter > 0) {
                    $newIncrementId = $numberPrefix . $orderIncrementId . '-' . $subIncrementIdCounter;
                } else {
                    $newIncrementId = $numberPrefix . $orderIncrementId;
                }
                $collection->clear();
                $collection->getSelect()->reset(\Magento\Framework\DB\Select::WHERE);
                $collection->getSelect()->where('increment_id = ?', $newIncrementId);
                if ($collection->count() > 0) {
                    $newIncrementId = false;
                    $subIncrementIdCounter++;
                } else {
                    $object->setIncrementId($newIncrementId);
                    break;
                }
            }
        }
        return $this;
    }

}
