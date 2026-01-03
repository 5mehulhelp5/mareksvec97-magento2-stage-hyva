<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Quote\Api;

use Exception;
use Magento\Quote\Api\CartRepositoryInterface as Subject;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartExtensionInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartSearchResultsInterface;
use Psr\Log\LoggerInterface;

class CartRepository
{
    protected CartExtensionFactory $cartExtensionFactory;
    protected LoggerInterface $logger;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        LoggerInterface $logger
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->logger = $logger;
    }

    /**
     * @param Subject $subject
     * @param CartInterface $quote
     * @return CartInterface
     */
    public function afterGet(Subject $subject, CartInterface $quote): CartInterface
    {
        $this->setCustomerComment($quote);
        return $quote;
    }

    /**
     * @param Subject $subject
     * @param CartSearchResultsInterface $quoteSearchResult
     * @return CartSearchResultsInterface
     */
    public function afterGetList(Subject $subject, CartSearchResultsInterface $quoteSearchResult): CartSearchResultsInterface
    {
        foreach ($quoteSearchResult->getItems() as $quote) {
            $this->setCustomerComment($quote);
        }

        return $quoteSearchResult;
    }

    /**
     * @param CartInterface $quote
     */
    public function setCustomerComment(CartInterface $quote): void
    {
        try {
            $extensionAttributes = $quote->getExtensionAttributes();

            /** @var CartExtensionInterface $target */
            $target = $extensionAttributes ?? $this->cartExtensionFactory->create();
            $target->setCustomerComment($quote->getCustomerComment());

            $quote->setExtensionAttributes($target);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }
    }

    /**
     * @param Subject $subject
     * @param CartInterface $quote
     * @return array
     */
    public function beforeSave(Subject $subject, CartInterface $quote): array
    {
        try {
            $customerComment = $quote->getExtensionAttributes()->getCustomerComment();

            if (is_string($customerComment)) {
                $quote->setCustomerComment($customerComment);
            }
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }

        return [$quote];
    }
}
