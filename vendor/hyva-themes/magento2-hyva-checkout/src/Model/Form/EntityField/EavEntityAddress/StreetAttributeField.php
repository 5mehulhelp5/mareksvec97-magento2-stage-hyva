<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField\EavEntityAddress;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;
use Hyva\Checkout\Model\Form\EntityField\FormFieldDependencies;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Framework\App\ObjectManager;

class StreetAttributeField extends EavAttributeField
{
    protected CustomerAddressHelper $customerAddressHelper;

    public function __construct(
        FormFieldDependencies $context,
        ?CustomerAddressHelper $customerAddressHelper = null
    ) {
        $this->customerAddressHelper = $customerAddressHelper
            ?: ObjectManager::getInstance()->get(CustomerAddressHelper::class);
        parent::__construct($context);
    }

    public function getValue()
    {
        return parent::getValue() ?? '';
    }

    /**
     * Let's make sure this field can only have the amount of relatives
     * corresponding to the fixed amount in the system configuration.
     */
    public function assignRelative(EntityFormElementInterface $relative): self
    {
        $maxLines = $this->customerAddressHelper->getStreetLines();

        // Skip adding more relatives when the limit has been reached.
        if ($maxLines <= 0 || count($this->getRelatives()) === ($maxLines - 1)) {
            return $this;
        }

        return parent::assignRelative($relative);
    }
}
