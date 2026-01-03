<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Block\Adminhtml\System\Config;

use Magento\Customer\Model\Metadata\AddressMetadata;
use Magento\Customer\Model\Metadata\CustomerMetadata;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class HyvaThemesCheckout extends Template
{
    public const DISPENSABLE_ATTRIBUTES = [
        'created_in',
        'disable_auto_group_change',
        'website_id',
        'group_id',
        'created_at',
        'updated_at',
        'default_billing',
        'default_shipping',
        'password_hash',
        'rp_token',
        'rp_token_created_at',
        'store_id',
        'confirmation',
        'vat_is_valid',
        'vat_request_date',
        'vat_request_id',
        'vat_request_success',
        'region_id',
        'failures_num',
        'first_failure',
        'lock_expires'
    ];

    protected AddressMetadata $addressMetadata;
    protected CustomerMetadata $customerMetadata;

    public function __construct(
        Context $context,
        AddressMetadata $addressMetadata,
        CustomerMetadata $customerMetadata,
        array $data = []
    ) {
        $this->addressMetadata = $addressMetadata;
        $this->customerMetadata = $customerMetadata;

        parent::__construct($context, $data);
    }

    /**
     * @throws LocalizedException
     */
    public function _construct()
    {
        $config = $this->getDefaultFields();
        $this->setData('fields', $config);
    }

    /**
     * @throws LocalizedException
     */
    public function getDefaultFields(): array
    {
        $fields = [];

        $customerAttributes = $this->customerMetadata->getAllAttributesMetadata();
        $addressAttributes = $this->addressMetadata->getAllAttributesMetadata();

        foreach ($customerAttributes as $attribute) {
            if (! in_array($attribute->getAttributeCode(), self::DISPENSABLE_ATTRIBUTES)) {
                $fields['customer'][$attribute->getAttributeCode()] = [
                    'required' => (int) $attribute->isRequired(),
                    'user_defined' => $attribute->isUserDefined()
                ];
            }
        }

        foreach ($addressAttributes as $attribute) {
            if (! in_array($attribute->getAttributeCode(), self::DISPENSABLE_ATTRIBUTES)) {
                $lines = $attribute->getMultilineCount();

                if ($lines > 0) {
                    for ($i = 0; $i <= ($lines - 1); $i++) {
                        $fields['address'][$attribute->getAttributeCode(). '.' . $i] = [
                            'required' => (int) $attribute->isRequired(),
                            'user_defined' => $attribute->isUserDefined()
                        ];
                    }
                } else {
                    $fields['address'][$attribute->getAttributeCode()] = [
                        'required' => (int) $attribute->isRequired(),
                        'user_defined' => $attribute->isUserDefined()
                    ];
                }
            }
        }

        if (! empty($fields['address']) && $fields['customer']) {
            $fields['merged'] = array_merge($fields['address'], $fields['customer']);
        }

        return $fields;
    }
}
