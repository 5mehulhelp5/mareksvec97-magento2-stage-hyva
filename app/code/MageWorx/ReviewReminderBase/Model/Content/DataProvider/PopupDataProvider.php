<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataProvider;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Api\MobileDetectorAdapterInterface;
use MageWorx\ReviewReminderBase\Model\Content\ContainerManager\PopupContainerManager;
use MageWorx\ReviewReminderBase\Model\Content\DataContainerInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataProviderInterface;
use MageWorx\ReviewReminderBase\Model\Reminder\Source\Status;
use MageWorx\ReviewReminderBase\Model\Reminder\Source\Type;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Collection as ReminderCollection;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\CollectionFactory as ReminderCollectionFactory;
use MageWorx\ReviewReminderBase\Source\Unsubscribed as UnsubscribedOptions;
use UnexpectedValueException;

class PopupDataProvider implements DataProviderInterface
{
    /**
     * @var ReminderCollectionFactory
     */
    protected $reminderCollectionFactory;

    /**
     * @var PopupContainerManager
     */
    protected $popupContainerManager;

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
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var MobileDetectorAdapterInterface
     */
    protected $mobileDetectorAdapter;

    /**
     * PopupDataProvider constructor.
     *
     * @param ReminderCollectionFactory $reminderCollectionFactory
     * @param PopupContainerManager $popupContainerManager
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param DateTime $dateTime
     * @param EventManager $eventManager
     * @param StoreManagerInterface $storeManager
     * @param SerializerInterface $serializer
     * @param UnsubscribedOptions $unsubscribedOptions
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param MobileDetectorAdapterInterface $mobileDetectorAdapter
     */
    public function __construct(
        ReminderCollectionFactory $reminderCollectionFactory,
        PopupContainerManager $popupContainerManager,
        OrderCollectionFactory $orderCollectionFactory,
        DateTime $dateTime,
        EventManager $eventManager,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer,
        UnsubscribedOptions $unsubscribedOptions,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        MobileDetectorAdapterInterface $mobileDetectorAdapter
    ) {
        $this->reminderCollectionFactory = $reminderCollectionFactory;
        $this->popupContainerManager     = $popupContainerManager;
        $this->orderCollectionFactory    = $orderCollectionFactory;
        $this->dateTime                  = $dateTime;
        $this->eventManager              = $eventManager;
        $this->storeManager              = $storeManager;
        $this->serializer                = $serializer;
        $this->unsubscribedOptions       = $unsubscribedOptions;
        $this->customerSession           = $customerSession;
        $this->customerRepository        = $customerRepository;
        $this->mobileDetectorAdapter     = $mobileDetectorAdapter;
    }

    /**
     * @return DataContainerInterface[]|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRemindersData(): array
    {
        $customerData = $this->getCustomerData();

        if ($customerData === null) {
            return [];
        }

        $orderCollection = $this->getOrderCollection();

        if ($orderCollection && $orderCollection->count()) {

            /** @var Order $order */
            foreach ($orderCollection as $order) {

                $reminder = $this->getReminderCollection()->getFirstItem();
                $this->popupContainerManager->composeContainerData($order, $reminder);
            }
        }

        return $this->popupContainerManager->getFinalContainers();
    }

    /**
     * @return OrderCollection|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getOrderCollection(): ?OrderCollection
    {
        $reminderPeriod = $this->getReminderCollection()->getFirstItem()->getPeriod();

        if ($reminderPeriod === null) {
            return null;
        }

        $customerData = $this->getCustomerData();

        if ($customerData === null) {
            return null;
        }

        $customerEmail = $customerData['customer_email'];

        $orderDate = $this->getDateByPeriod($reminderPeriod);

        /** @var OrderCollection $collection */
        $collection = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('updated_at', ['lteq' => $orderDate])
            ->addFieldToFilter('status', 'complete')
            ->addFieldToFilter('store_id', $this->storeManager->getStore()->getId())
            ->addFieldToFilter('customer_email', $customerEmail);
            //->addFieldToFilter('customer_group_id', $this->getCustomerData()['customer_group_id']);

        $this->eventManager->dispatch(
            'mageworx_reviewreminderbase_popup_order_collection',
            ['collection' => $collection, 'date' => $orderDate]
        );

        if (!$collection instanceof OrderCollection) {
            throw new UnexpectedValueException('Order collection has wrong type.');
        }

        return $collection;
    }

    /**
     * We look for the ONE popup reminder with max priority and minimum silence period to cover maximum orders.
     * The different reminders content will be ignored during this process.
     *
     * @return ReminderCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getReminderCollection(): ReminderCollection
    {
        if (!$this->reminderCollection) {

            /** @var ReminderCollection $reminderCollection */
            $reminderCollection = $this->reminderCollectionFactory->create();
            $reminderCollection->addFieldToFilter(ReminderInterface::STATUS, Status::ENABLE);
            $reminderCollection->addFieldToFilter(ReminderInterface::TYPE, Type::TYPE_POPUP);
            $reminderCollection->addCustomerGroupFilter($this->getCustomerData()['customer_group_id']);
            $reminderCollection->addStoreFilter($this->storeManager->getStore()->getId());

            if ($this->mobileDetectorAdapter->isMobile()) {
                $reminderCollection->addFieldToFilter(ReminderInterface::DISPLAY_ON_MOBILE, 1);
            }

            $reminderCollection->addOrder(ReminderInterface::PERIOD, $reminderCollection::SORT_ORDER_ASC);
            $reminderCollection->addOrder(ReminderInterface::PRIORITY);
            $reminderCollection->setPageSize(1)->setCurPage(1);

            $this->eventManager->dispatch(
                'mageworx_reviewreminderbase_popup_reminder_collection',
                ['collection' => $reminderCollection]
            );

            if (!$reminderCollection instanceof ReminderCollection) {
                throw new UnexpectedValueException('Reminder collection has wrong type');
            }

            $this->reminderCollection = $reminderCollection;
        }

        return $this->reminderCollection;
    }

    /**
     * @param int $period
     * @return string
     */
    protected function getDateByPeriod($period): string
    {
        $periodCondition = $this->dateTime->gmtDate() . ' - ' . $period . 'days';

        return date('Y-m-d', strtotime($periodCondition));
    }

    /**
     * Format of output array:
     *
     * [
     *      'customer_email' => ...,
     *      'customer_group_id' => ...
     * ]
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomerData(): ?array
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerRepository->getById($this->customerSession->getId());

            return [
                'customer_email'    => $customer->getEmail(),
                'customer_group_id' => $customer->getGroupId()
            ];
        }

        $email = $this->customerSession->getCustomerPreviousEmail();

        if ($email) {
            return [
                'customer_email'    => $email,
                'customer_group_id' => GroupInterface::NOT_LOGGED_IN_ID
            ];
        }

        return null;
    }
}
