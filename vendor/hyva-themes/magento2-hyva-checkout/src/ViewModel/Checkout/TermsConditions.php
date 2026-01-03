<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout;

use Magento\CheckoutAgreements\Api\CheckoutAgreementsListInterface;
use Magento\CheckoutAgreements\Model\Api\SearchCriteria\ActiveStoreAgreementsFilter;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class TermsConditions implements ArgumentInterface
{
    private ActiveStoreAgreementsFilter $activeStoreAgreementsFilter;
    private CheckoutAgreementsListInterface $checkoutAgreementsList;

    public function __construct(
        CheckoutAgreementsListInterface $checkoutAgreementsList,
        ActiveStoreAgreementsFilter $activeStoreAgreementsFilter
    ) {
        $this->checkoutAgreementsList = $checkoutAgreementsList;
        $this->activeStoreAgreementsFilter = $activeStoreAgreementsFilter;
    }

    public function hasAgreements(): bool
    {
        $agreements = $this->checkoutAgreementsList->getList(
            $this->activeStoreAgreementsFilter->buildSearchCriteria()
        );

        return count($agreements) !== 0;
    }
}
