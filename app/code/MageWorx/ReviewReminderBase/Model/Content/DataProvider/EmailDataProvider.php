<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataProvider;

use Exception;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManager\EmailContainerManager;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\EmailDataContainer;
use MageWorx\ReviewReminderBase\Model\Content\DataProviderInterface;
use MageWorx\ReviewReminderBase\Model\Reminder\Source\Status;
use MageWorx\ReviewReminderBase\Model\Reminder\Source\Type;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Collection as ReminderCollection;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\CollectionFactory as ReminderCollectionFactory;
use MageWorx\ReviewReminderBase\Source\Unsubscribed as UnsubscribedOptions;

class EmailDataProvider implements DataProviderInterface
{
    /**
     * @var ReminderCollectionFactory
     */
    protected $reminderCollectionFactory;

    /**
     * @var EmailContainerManager
     */
    protected $emailContainerManager;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var ReminderCollection
     */
    protected $reminderCollection;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var UnsubscribedOptions
     */
    protected $unsubscribedOptions;

    /**
     * EmailDataProvider constructor.
     *
     * @param ReminderCollectionFactory $reminderCollectionFactory
     * @param EmailContainerManager $emailContainerManager
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param DateTime $dateTime
     * @param EventManager $eventManager
     * @param StoreManagerInterface $storeManager
     * @param SerializerInterface $serializer
     * @param UnsubscribedOptions $unsubscribedOptions
     */
    public function __construct(
        ReminderCollectionFactory $reminderCollectionFactory,
        EmailContainerManager $emailContainerManager,
        OrderCollectionFactory $orderCollectionFactory,
        DateTime $dateTime,
        EventManager $eventManager,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        UnsubscribedOptions $unsubscribedOptions
    ) {
        $this->reminderCollectionFactory = $reminderCollectionFactory;
        $this->emailContainerManager     = $emailContainerManager;
        $this->orderCollectionFactory    = $orderCollectionFactory;
        $this->dateTime                  = $dateTime;
        $this->eventManager              = $eventManager;
        $this->storeManager              = $storeManager;
        $this->serializer                = $serializer;
        $this->unsubscribedOptions       = $unsubscribedOptions;
    }

    /**
     * @return EmailDataContainer[]|void
     * @throws Exception
     */
    public function getRemindersData(): array
    {
        $result = [];

        foreach ($this->getActiveStores() as $storeId) {

            $orderCollection = $this->getOrderCollection($storeId);

            if ($orderCollection && $orderCollection->count()) {

                /** @var Order $order */
                foreach ($orderCollection as $order) {

                    $period = $this->getPeriodByDate($order->getUpdatedAt());

                    if ($period !== null) {

                        $reminder = $this->getReminderCollection($storeId)->getItemByParams(
                            $period,
                            $order->getCustomerGroupId(),
                            $order->getStoreId()
                        );

                        if ($reminder) {
                            $this->emailContainerManager->composeContainerData($order, $reminder);
                        }
                    }
                }
            }

            $result = array_merge($result, $this->emailContainerManager->getFinalContainers());

            $this->reminderCollection = null;
        }

        return $result;
    }

    /**
     * @param int $storeId
     * @return OrderCollection
     * @throws Exception
     */
    protected function getOrderCollection($storeId): ?OrderCollection
    {
        $reminderPeriods = $this->getReminderCollection($storeId)->getColumnValues(ReminderInterface::PERIOD);

        if (!$reminderPeriods) {
            return null;
        }

        $orderDates = $this->getDatesByPeriods($reminderPeriods);

        $collection = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('updated_at', ['regexp' => implode('|', $orderDates)])
            ->addFieldToFilter('status', 'complete')
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('customer_email', ['notnull' => true]);

        $excludedEmails = array_column($this->unsubscribedOptions->getAllOptions(), 'label');

        if ($excludedEmails) {
            $collection->addFieldToFilter('customer_email', ['nin' => $excludedEmails]);
        }

        $this->eventManager->dispatch(
            'mageworx_reviewreminderbase_email_order_collection',
            ['collection' => $collection, 'dates' => $orderDates]
        );

        if (!$collection instanceof OrderCollection) {
            throw new Exception('Order collection has wrong type.');
        }

        return $collection;
    }

    /**
     * @return ReminderCollection
     * @throws Exception
     */
    protected function getReminderCollection($storeId): ReminderCollection
    {
        if (!$this->reminderCollection) {

            $reminderCollection = $this->reminderCollectionFactory->create();
            $reminderCollection->addFieldToFilter(ReminderInterface::STATUS, Status::ENABLE);
            $reminderCollection->addFieldToFilter(ReminderInterface::TYPE, Type::TYPE_EMAIL);
            $reminderCollection->addStoreFilter($storeId);

            $this->eventManager->dispatch(
                'mageworx_reviewreminderbase_email_reminder_collection',
                ['collection' => $reminderCollection]
            );

            if (!$reminderCollection instanceof ReminderCollection) {
                throw new Exception('Reminder collection has wrong type');
            }

            $this->reminderCollection = $reminderCollection;
        }

        return $this->reminderCollection;
    }

    /**
     * @param array $periods
     * @return array
     */
    protected function getDatesByPeriods(array $periods): array
    {
        $periods = array_unique($periods);
        $dates   = [];
        $gmtDate = $this->dateTime->gmtDate();

        foreach ($periods as $period) {
            $periodCondition = $gmtDate . ' - ' . $period . 'days';
            $date            = date('Y-m-d', strtotime($periodCondition));
            $dates[]         = $date;
        }

        return $dates;
    }

    /**
     * @param string $date //format: 2020-02-26 07:43:53
     * @return int
     * @throws Exception
     */
    protected function getPeriodByDate(string $date): ?int
    {
        list($date) = explode(' ', $date);
        $daysInterval = (new \DateTime($date))->diff(new \DateTime($this->dateTime->gmtDate('Y-m-d')))->format('%a');

        return $daysInterval ? (int)$daysInterval : null;
    }

    /**
     * Retrieves list of active store IDs
     *
     * @return int[]
     */
    protected function getActiveStores(): array
    {
        if ($this->storeManager->isSingleStoreMode()) {
            return [$this->storeManager->getDefaultStoreView()->getId()];
        }

        $stores = [];
        foreach ($this->storeManager->getStores() as $store) {
            if (!$store->getIsActive()) {
                continue;
            }

            $stores[] = (int)$store->getId();
        }

        return $stores;
    }
}
