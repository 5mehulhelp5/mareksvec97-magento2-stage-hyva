<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

class CheckoutInformationProvider
{
    /** @var CheckoutInformationInterface[] */
    protected array $checkoutPool;

    /**
     * @param CheckoutInformationInterface[] $checkoutPool
     */
    public function __construct(
        array $checkoutPool = []
    ) {
        $this->checkoutPool = $checkoutPool;
    }

    /**
     * @return array<string, CheckoutInformationInterface>
     */
    public function getList(): array
    {
        foreach ($this->checkoutPool as $checkoutFallback) {
            if ($checkoutFallback instanceof CheckoutInformationInterface) {
                $list[$checkoutFallback->getNamespace()] = $checkoutFallback;
            }
        }

        return $list ?? [];
    }
}
