<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Sales\Api;

use Exception;
use Magento\Sales\Api\Data\OrderStatusHistoryExtensionFactory;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface as Subject;
use Psr\Log\LoggerInterface;

class OrderStatusHistoryRepository
{
    protected OrderStatusHistoryExtensionFactory $orderStatusHistoryExtensionFactory;
    protected LoggerInterface $logger;

    /**
     * @param OrderStatusHistoryExtensionFactory $orderStatusHistoryExtensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderStatusHistoryExtensionFactory $orderStatusHistoryExtensionFactory,
        LoggerInterface $logger
    ) {
        $this->orderStatusHistoryExtensionFactory = $orderStatusHistoryExtensionFactory;
        $this->logger = $logger;
    }

    /**
     * @param Subject $subject
     * @param OrderStatusHistoryInterface $orderStatusHistory
     * @return OrderStatusHistoryInterface
     */
    public function afterGet(Subject $subject, OrderStatusHistoryInterface $orderStatusHistory): OrderStatusHistoryInterface
    {
        $this->setIsCustomerComment($orderStatusHistory);
        return $orderStatusHistory;
    }

    /**
     * @param Subject $subject
     * @param $orderStatusHistorySearchResult
     * @return mixed
     */
    public function afterGetList(Subject $subject, $orderStatusHistorySearchResult)
    {
        foreach ($orderStatusHistorySearchResult->getItems() as $quote) {
            $this->setIsCustomerComment($quote);
        }

        return $orderStatusHistorySearchResult;
    }

    /**
     * @param OrderStatusHistoryInterface $orderStatusHistory
     */
    public function setIsCustomerComment(OrderStatusHistoryInterface $orderStatusHistory)
    {
        try {
            $extensionAttributes = $orderStatusHistory->getExtensionAttributes();

            /** @var OrderStatusHistoryInterface $target */
            $target = $extensionAttributes ?? $this->orderStatusHistoryExtensionFactory->create();
            $target->setIsCustomerComment($orderStatusHistory->getIsCustomerComment());

            $orderStatusHistory->setExtensionAttributes($target);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }
    }

    /**
     * @param Subject $subject
     * @param OrderStatusHistoryInterface $orderStatusHistory
     * @return OrderStatusHistoryInterface[]
     */
    public function beforeSave(Subject $subject, OrderStatusHistoryInterface $orderStatusHistory): array
    {
        try {
            $isCustomerComment = $orderStatusHistory->getExtensionAttributes()->getIsCustomerComment();

            if (is_bool($isCustomerComment)) {
                $orderStatusHistory->setIsCustomerComment($isCustomerComment);
            }
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }

        return [$orderStatusHistory];
    }
}
