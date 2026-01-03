<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\CheckoutPayPal\ViewModel\Payment\Method;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Paypal\Model\Payflow\Transparent as PayflowCc;

class PayflowproCc extends PayflowCc implements ArgumentInterface
{

}
