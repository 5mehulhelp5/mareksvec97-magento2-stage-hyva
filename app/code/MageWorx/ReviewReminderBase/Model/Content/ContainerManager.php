<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content;

use Exception;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use MageWorx\ReviewReminderBase\Api\Data\ReminderInterface;
use MageWorx\ReviewReminderBase\Model\Content\Converter\Component\ComponentProductConverter;

abstract class ContainerManager implements ContainerManagerInterface
{
    /**
     * @var DataContainerFactory
     */
    protected $dataContainerFactory;

    /**
     * @var DataContainer[]
     */
    protected $dataContainers = [];

    /**
     * @var DataModifierInterface[]
     */
    protected $modifiers;

    /**
     * @var ComponentProductConverter
     */
    protected $productConverter;

    /**
     * @param Order $order
     * @param ReminderInterface $reminder
     * @param DataContainer $dataContainer
     * @return void
     */
    abstract protected function convertSpecificData(
        Order $order,
        ReminderInterface $reminder,
        DataContainer $dataContainer
    ): void;

    /**
     * DataProvider constructor.
     *
     * @param DataContainerFactory $dataContainerFactory
     * @param ComponentProductConverter $productConverter
     * @param array $modifiers
     */
    public function __construct(
        DataContainerFactory $dataContainerFactory,
        ComponentProductConverter $productConverter,
        array $modifiers = []
    ) {
        $this->dataContainerFactory = $dataContainerFactory;
        $this->modifiers            = $modifiers;
        $this->productConverter     = $productConverter;
    }

    /**
     * @param Order $order
     * @param ReminderInterface $reminder
     */
    public function composeContainerData(Order $order, ReminderInterface $reminder): void
    {
        $key = $order->getCustomerEmail() . '|' . $reminder->getEmailTemplateId();

        if (isset($this->dataContainers[$key])) {
            $dataContainer = $this->dataContainers[$key];
        } else {
            $dataContainer = $this->dataContainerFactory->create($reminder->getType());
        }

        $dataContainer->setCustomerEmail($order->getCustomerEmail());
        $dataContainer->setCustomerFirstName($order->getCustomerFirstname());
        $dataContainer->setCustomerLastName($order->getCustomerLastname());
        $dataContainer->setCustomerMiddletName($order->getCustomerMiddlename());
        $dataContainer->setCustomerName($order->getCustomerName());
        $dataContainer->setStoreId((int)$order->getStoreId());
        $dataContainer->setReminderId($reminder->getReminderId());

        $this->convertSpecificData($order, $reminder, $dataContainer);

        $products = [];

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $products[$orderItem->getProductId()]
                = $this->productConverter->convertOrderItemToComponentProduct($orderItem);
        }

        $dataContainer->addOrderProducts((int)$order->getId(), $products);

        $this->dataContainers[$key] = $dataContainer;
    }

    /**
     * For using only from modifiers
     *
     * @return EmailDataContainer[]
     */
    public function getCurrentContainers(): array
    {
        return $this->dataContainers;
    }

    /**
     * @return EmailDataContainer[]
     * @throws Exception
     */
    public function getFinalContainers(): array
    {
        foreach ($this->modifiers as $modifier) {

            if (!$modifier instanceof DataModifierInterface) {
                throw new Exception('Incorrect type for email modifier');
            }

            $modifier->modify($this);
        }

        return $this->dataContainers;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function removeByKey(string $key): bool
    {
        if (!empty($this->dataContainers[$key])) {
            unset ($this->dataContainers[$key]);

            return true;
        }

        return false;
    }

    /**
     * Output array structure:
     *
     * @return array
     */
    public function getProductIds(): array
    {
        $productIds = [];

        /** @var DataContainer $emailContainer */
        foreach ($this->dataContainers as $dataContainer) {
            $productIds = array_merge(
                $productIds,
                $dataContainer->getProductIds()
            );
        }

        return $productIds;
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        foreach ($this->dataContainers as $dataContainer) {
            return $dataContainer->getStoreId();
        }
    }
}
