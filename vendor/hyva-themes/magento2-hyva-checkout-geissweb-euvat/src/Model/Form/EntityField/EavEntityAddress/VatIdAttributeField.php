<?php
declare(strict_types=1);

namespace Hyva\GeisswebEuvatCheckout\Model\Form\EntityField\EavEntityAddress;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeField;

class VatIdAttributeField extends EavAttributeField
{
    public function isAutoSave(): bool
    {
        return false;
    }
}
