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
 
namespace Magehq\CustomOrderNumber\Helper;

class Generator extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magehq\CustomOrderNumber\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $coreDate;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Generator constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Config $configHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magehq\CustomOrderNumber\Logger\Logger $logger
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magehq\CustomOrderNumber\Helper\Config $configHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magehq\CustomOrderNumber\Logger\Logger $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->coreDate = $coreDate;
        $this->eventManager = $context->getEventManager();
    }

    /**
     * Generate increment ID for orders/invoices/shipment/credit memos
     *
     * @param Sequence $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function generateIncrementId($object, $entityType, $originalSequence)
    {
        $storeId = $object->getStoreId();

        if (!$this->configHelper->isModuleEnabled()) {
            return $originalSequence;
        }

        try {
            if (!$originalSequence) {
                // There was a problem. Don't hook in either.
                return $originalSequence;
            }

            // Is the module enabled?
            if (!$this->configHelper->isModuleEnabled()) {
                return $originalSequence;
            }

            // Is supported entity_type?
            if (!$this->configHelper->isSupportedEntityType($entityType)) {
                return $originalSequence;
            }

            // Is (order/invoice/...) number customizer enabled for this store ID?
            if (!$this->configHelper->getConfigFlag($entityType, 'enabled', $storeId)) {
                return $originalSequence;
            }

            // Shall we create a new number or will the order number be used instead?
            // Just for invoice/shipment/credit memo numbers
            if ($entityType == 'invoice' || $entityType == 'shipment' || $entityType == 'creditmemo') {
                if ($this->configHelper->getConfigFlag($entityType, 'same_as_order', $storeId)) {
                    return $originalSequence;
                }
            }

            $newIncrementId = $this->generateCustomIncrementId($entityType, $storeId);
            if (!$newIncrementId || empty($newIncrementId)) {
                $this->logger->warning(
                    sprintf(
                        'Attention: For %1 ID %2, an empty increment ID was generated: %3',
                        $entityType,
                        $originalSequence,
                        $newIncrementId
                    )
                );
                return $originalSequence;
            }

            // Check if increment ID exists already, if yes return non-existing increment ID
            if ($this->isIncrementIdExisting($entityType, $newIncrementId)) {
                $this->logger->warning(
                    sprintf(
                        'Attention: Generated %1 increment_id "%2" already exists. Using Magento increment_id "%3" instead.',
                        $entityType,
                        $newIncrementId,
                        $originalSequence
                    )
                );
                return $originalSequence;
            }
        } catch (\Exception $e) {
            $this->logger->alert('Exception while generating new increment ID: '. $e->getMessage(). ' - ' . $e->getTraceAsString());
            return $originalSequence;
        }

        return $newIncrementId;
    }


    protected function isIncrementIdExisting($entityType, $incrementId)
    {
        if ($entityType == \Magento\Sales\Model\Order::ENTITY) {
            $entity = '\Magento\Sales\Model\Order';
        } else {
            if ($entityType == 'invoice') {
                $entity = '\Magento\Sales\Model\Order\Invoice';
            } else {
                if ($entityType == 'shipment') {
                    $entity = '\Magento\Sales\Model\Order\Shipment';
                } else {
                    if ($entityType == 'creditmemo') {
                        $entity = '\Magento\Sales\Model\Order\Creditmemo';
                    } else {
                        $this->logger->warning(
                            __('Attention: Specified entity %1 is not supported by the extension.', $entityType)
                        );
                        return true;
                    }
                }
            }
        }

        // Check if increment ID exists
        $objectIds = $this->objectManager->create($entity)
            ->getCollection()
            ->addAttributeToFilter('increment_id', $incrementId)
            ->getAllIds();
        if (!empty($objectIds)) {
            return true;
        }
        return false;
    }

    protected function generateCustomIncrementId($entityType, $storeId)
    {
        $incrementIdFormat = $this->configHelper->getConfigValue(
            $entityType,
            'id_format',
            $storeId
        ); // Increment ID format
        $incrementBy = $this->configHelper->prepareIntPositive(
            $this->configHelper->getConfigValue($entityType, 'increment_by', $storeId)
        ); // Increase counter by X
        $incrementPadding = $this->configHelper->prepareIntPositive(
            $this->configHelper->getConfigValue($entityType, 'padding', $storeId)
        ); // Counter padding
        $resetCounter = $this->configHelper->getConfigValue(
            $entityType,
            'reset_counter',
            $storeId
        ); // Don't reset, daily, weekly, ...
        $lastResetDate = $this->configHelper->getConfigValueFromDb(
            $entityType,
            'reset_date',
            $storeId
        ); // Last reset date
        $countFromValue = $this->configHelper->prepareIntPositive(
            $this->configHelper->getConfigValue($entityType, 'count_from', $storeId)
        ); // Start counting from...
        $forceResetCounterNow = $this->configHelper->getConfigValueFromDb(
            $entityType,
            'force_reset_counter',
            $storeId
        );

        $incrementCounter = $this->configHelper->getConfigValueFromDb(
            $entityType,
            'increment_counter',
            $storeId
        );
        $currentCounterValue = $this->configHelper->prepareIntPositive($incrementCounter->getValue());
        if ($incrementCounter && $currentCounterValue > 0) {
            $lastResetDateValue = $lastResetDate->getValue();
            if ($resetCounter !== '' && !empty($lastResetDateValue)) {
                // Counter must be reset daily/weekly/yearly
                $dateFormat = false;
                if ($resetCounter == "daily") {
                    $dateFormat = "Y-m-d";
                } elseif ($resetCounter == "monthly") {
                    $dateFormat = "Y-m";
                } elseif ($resetCounter == "yearly") {
                    $dateFormat = "Y";
                }
                if ($dateFormat) {
                    $dateHasChanged = false;
                    /*if (!$lastResetDateValue) {
                        $dateHasChanged = true;
                    }*/
                    if ($this->coreDate->date($dateFormat) != $this->coreDate->date($dateFormat, $lastResetDateValue)) {
                        $dateHasChanged = true;
                    }
                    // Reset counter
                    if ($dateHasChanged) {
                        $currentCounterValue = $countFromValue;
                    }
                }
            }

            if ($incrementBy < 1) {
                $incrementBy = 1;
            }
            $newCounterValue = $currentCounterValue + $incrementBy;

            if ($forceResetCounterNow->getValue() === '1') {
                $newCounterValue = $countFromValue;
                $forceResetCounterNow->setValue('')->save();
            }
        } else {
            $newCounterValue = $countFromValue;
        }

        // Save current date
        $dateToday = $this->coreDate->date("Y-m-d");
        $lastResetDate->setValue($dateToday)->save();
        // Update last ID field
        $incrementCounter->setValue($newCounterValue)->save();

        // Padding
        if ($incrementPadding > 0) {
            $newCounterValue = str_pad($newCounterValue, $incrementPadding, 0, STR_PAD_LEFT);
        }

        // Replace variables
        $replaceableVariables = [
            '/%d%/' => $this->coreDate->date('j'),
            '/%dd%/' => $this->coreDate->date('d'),
            '/%m%/' => $this->coreDate->date('n'),
            '/%mm%/' => $this->coreDate->date('m'),
            '/%yy%/' => $this->coreDate->date('y'),
            '/%yyyy%/' => $this->coreDate->date('Y'),
            '/%h%/' => $this->coreDate->date('G'),
            '/%hh%/' => $this->coreDate->date('H'),
            '/%ii%/' => $this->coreDate->date('i'),
            '/%ss%/' => $this->coreDate->date('s'),
            '/%store_id%/' => $storeId,
            '/%counter%/' => $newCounterValue,
            '/%rand3%/' => rand(100, 999),
            '/%rand4%/' => rand(1000, 9999),
            '/%rand5%/' => rand(10000, 99999),
            '/%rand6%/' => rand(100000, 999999),
            '/%rand7%/' => rand(1000000, 9999999),
            '/%rand8%/' => rand(10000000, 99999999),
            '/%rand9%/' => rand(100000000, 999999999),
        ];

        // Ability to add custom variables to the increment_id format using an event
        $transportObject = new \Magento\Framework\DataObject;
        $transportObject->setCustomVariables([]);
        $transportObject->setExistingVariables($replaceableVariables);
        $this->eventManager->dispatch(
            'magesales_customordernumber_replace_variables_before',
            ['transport' => $transportObject]
        );
        $replaceableVariables = array_merge($replaceableVariables, $transportObject->getCustomVariables());

        // Generate new increment_id
        $newIncrementId = preg_replace(
            array_keys($replaceableVariables),
            array_values($replaceableVariables),
            $incrementIdFormat
        );

        #var_dump($incrementIdFormat, $newIncrementId, $this->_isIncrementIdExisting($newIncrementId));
        #die();

        return $newIncrementId;
    }
}
