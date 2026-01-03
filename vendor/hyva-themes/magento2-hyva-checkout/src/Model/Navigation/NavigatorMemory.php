<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Navigation;

use Hyva\Checkout\Model\Session as ObsoleteCheckoutSession;
use Magento\Checkout\Model\Session as MagentoCheckoutSession;

class NavigatorMemory
{
    private ObsoleteCheckoutSession $obsoleteCheckoutSession;
    private MagentoCheckoutSession $magentoCheckoutSession;

    public function __construct(
        ObsoleteCheckoutSession $obsoleteCheckoutSession,
        MagentoCheckoutSession $magentoCheckoutSession
    ) {
        $this->obsoleteCheckoutSession = $obsoleteCheckoutSession;
        $this->magentoCheckoutSession = $magentoCheckoutSession;
    }

    public function getMagentoCheckoutSession(): MagentoCheckoutSession
    {
        return $this->magentoCheckoutSession;
    }

    public function getAttempts(): int
    {
        return $this->getData('attempts', 0);
    }

    public function isFirstAttempt(): bool
    {
        return in_array($this->getAttempts(), [0, 1], true);
    }

    public function forget(): self
    {
        $data = array_filter($this->getData(), fn ($key) => $this->isPersistent($key), ARRAY_FILTER_USE_KEY);

        $this->getMagentoCheckoutSession()->setHyvaCheckout($data);
        return $this;
    }

    public function destroy(): self
    {
        $this->getMagentoCheckoutSession()->unsHyvaCheckout();

        return $this;
    }

    /**
     * @internal only used for making step management backwards compatible using the session.
     */
    public function getObsoleteCheckoutSession(): ObsoleteCheckoutSession
    {
        return $this->obsoleteCheckoutSession;
    }

    public function setData(string $key, $value, bool $persistent = false): self
    {
        $data = $this->getData();
        $data[$key] = $value;

        if ($persistent) {
            $this->setPersistent($key, $persistent);
        }

        $this->getMagentoCheckoutSession()->setHyvaCheckout($data);
        return $this;
    }

    public function getData(?string $key = null, $default = null)
    {
        $data = $this->getMagentoCheckoutSession()->getHyvaCheckout() ?? [];

        return $key ? ($data[$key] ?? $default) : $data;
    }

    private function isPersistent(string $key): bool
    {
        $persistent = $this->getPersistent();

        return ! in_array($key, array_keys($persistent)) || $persistent[$key] ?? false;
    }

    private function getPersistent(): array
    {
        $persistent = $this->getMagentoCheckoutSession()->getHyvaCheckoutPersistantKeys();

        if (! is_array($persistent)) {
            $persistent = [];

            $this->getMagentoCheckoutSession()->setHyvaCheckoutPersistentKeys($persistent);
        }

        return $persistent;
    }

    private function setPersistent(string $key, bool $value = true): self
    {
        $persistent = $this->getPersistent();
        $persistent[$key] = $value;

        $this->getMagentoCheckoutSession()->setHyvaCheckoutPersistentKeys($persistent);
        return $this;
    }
}
