<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

use Hyva\Checkout\Model\Form\EntityForm\EavAttributeBillingAddressForm;
use Hyva\Checkout\Model\Form\EntityForm\EavAttributeShippingAddressForm;

class EntityFormProvider implements EntityFormProviderInterface
{
    /** @var EntityFormInterface[] */
    protected array $entityForms;

    /**
     * @param EntityFormInterface[] $entityForms
     */
    public function __construct(
        array $entityForms = []
    ) {
        $this->entityForms = $entityForms;
    }

    public function getByName(string $name, array $values = []): ?EntityFormInterface
    {
        return $this->entityForms[$name] ?? null;
    }

    public function getShippingAddressForm(): EntityFormInterface
    {
        return $this->getByName(EavAttributeShippingAddressForm::FORM_NAMESPACE);
    }

    public function getBillingAddressForm(): EntityFormInterface
    {
        return $this->getByName(EavAttributeBillingAddressForm::FORM_NAMESPACE);
    }
}
