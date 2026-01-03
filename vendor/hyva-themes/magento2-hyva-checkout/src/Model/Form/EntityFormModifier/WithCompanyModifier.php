<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Form\AbstractEntityForm;
use Hyva\Checkout\Model\Form\AbstractEntityFormModifier;
use Hyva\Checkout\Model\Form\EntityField\AbstractEntityField;
use Magento\Config\Model\Config\Source\Nooptreq;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class WithCompanyModifier extends AbstractEntityFormModifier
{
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function apply(AbstractEntityForm $form): AbstractEntityForm
    {
        return $form->modifyField(AddressInterface::KEY_COMPANY, function (AbstractEntityField $field) {
            $companyShowConfig = $this->scopeConfig->getValue(
                'customer/address/company_show',
                ScopeInterface::SCOPE_STORE
            ) ?? Nooptreq::VALUE_NO;

            switch ($companyShowConfig) {
                case Nooptreq::VALUE_NO:
                    $field->hide();
                    break;
                case Nooptreq::VALUE_OPTIONAL:
                    $field->show()->setData('is_required', false);
                    break;
                case Nooptreq::VALUE_REQUIRED:
                    $field->show()->setData('is_required', true);
                    break;
            }
        });
    }
}
