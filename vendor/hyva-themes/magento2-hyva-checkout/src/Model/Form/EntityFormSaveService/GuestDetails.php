<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormSaveService;

use Hyva\Checkout\Model\Form\AbstractEntityFormSaveService;
use Hyva\Checkout\Model\Form\EntityForm\GuestDetailsForm;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;

class GuestDetails extends AbstractEntityFormSaveService
{
    private Session $sessionCheckout;
    private CartRepositoryInterface $quoteRepository;

    public function __construct(
        Session $sessionCheckout,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(EntityFormInterface $form): EntityFormInterface
    {
        $emailAddressField = $form->getField(GuestDetailsForm::FIELD_EMAIL);

        if (! $emailAddressField) {
            throw new CouldNotSaveException(
                __('Email address field could not be found.')
            );
        }

        if (! $emailAddressField->hasValue()) {
            throw new CouldNotSaveException(
                __('Email address cannot be empty.')
            );
        }

        $quote = $this->sessionCheckout->getQuote();

        $quote->setCustomerEmail($emailAddressField->getValue());
        $this->quoteRepository->save($quote);

        return $form;
    }
}
