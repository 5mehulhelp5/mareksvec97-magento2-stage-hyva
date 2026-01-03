<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout;

use Exception;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magewirephp\Magewire\Component;

class CustomerComment extends Component
{
    public ?string $comment = null;
    public bool $saved = false;

    protected SessionCheckout $sessionCheckout;
    protected CartRepositoryInterface $quoteRepository;

    public function __construct(
        SessionCheckout $sessionCheckout,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function mount(): void
    {
        $quote = $this->sessionCheckout->getQuote();
        $comment = $quote->getExtensionAttributes()->getCustomerComment();

        $this->comment = $comment;
    }

    public function updatingComment(string $value): string
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $quote->getExtensionAttributes()->setCustomerComment($value);

            $this->quoteRepository->save($quote);
            $this->saved = true;
        } catch (LocalizedException| Exception $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving your order comment. Please try again.');
        }

        return $value;
    }
}
