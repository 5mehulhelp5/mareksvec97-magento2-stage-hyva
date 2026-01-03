<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\Data\CartExtensionFactory as QuoteExtensionFactory;
use Magento\Quote\Api\Data\CartExtensionInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use Psr\Log\LoggerInterface;

class HyvaCheckoutCheckoutSubmitAllAfter implements ObserverInterface
{
    protected OrderStatusHistoryRepositoryInterface $orderStatusRepository;
    protected OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory;
    protected LoggerInterface $logger;
    protected QuoteExtensionFactory $quoteExtensionFactory;

    public function __construct(
        OrderStatusHistoryRepositoryInterface $orderStatusRepository,
        OrderStatusHistoryInterfaceFactory $orderStatusHistoryInterfaceFactory,
        LoggerInterface $logger,
        QuoteExtensionFactory $quoteExtensionFactory
    ) {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStatusHistoryInterfaceFactory = $orderStatusHistoryInterfaceFactory;
        $this->logger = $logger;
        $this->quoteExtensionFactory = $quoteExtensionFactory;
    }

    /**
     * Save the customer comment as an order status history item when available.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (! $observer->hasData('order') || ! $observer->hasData('quote')) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $observer->getOrder();
        /** @var CartInterface $quote */
        $quote = $observer->getQuote();
        /** @var string|null $comment */
        $comment = $this->getQuoteExtensionAttributes($quote)->getCustomerComment();

        if ($comment) {
            $statusHistoryItem = $this->orderStatusHistoryInterfaceFactory->create();

            $statusHistoryItem->setParentId($order->getId());
            $statusHistoryItem->setComment($comment);
            $statusHistoryItem->setIsVisibleOnFront(true);
            $statusHistoryItem->setIsCustomerNotified(false);
            $statusHistoryItem->setStatus($order->getStatus());

            $statusHistoryItem->getExtensionAttributes()->setIsCustomerComment(true);

            try {
                $this->orderStatusRepository->save($statusHistoryItem);
            } catch (CouldNotSaveException $exception) {
                $this->logger->critical(
                    sprintf('Order comment for quote id "%s" could not be saved', $quote->getId()),
                    ['exception' => $exception]
                );
            }
        }
    }

    public function getQuoteExtensionAttributes(CartInterface $quote): CartExtensionInterface
    {
        return $quote->getExtensionAttributes() ?? $this->quoteExtensionFactory->create();
    }
}
