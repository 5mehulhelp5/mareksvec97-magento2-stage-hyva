<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation;

class PlaceOrderRedirect extends Redirect
{
    private string $orderSuccessTemplate = 'Hyva_Checkout::main/place-order/redirect.phtml';

    /**
     * Sets the template that should be used while redirecting.
     */
    public function withRedirectTemplate(string $template): static
    {
        $this->orderSuccessTemplate = $template;

        return $this;
    }

    /**
     * Returns the order success template.
     *
     * @internal This getter is exempt from the standard evaluation result restrictions
     *           as the template path is used internally and not transmitted to the frontend.
     */
    public function getRedirectTemplate(): string
    {
        return $this->orderSuccessTemplate;
    }
}
