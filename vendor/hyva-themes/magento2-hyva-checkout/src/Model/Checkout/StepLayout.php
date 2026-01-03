<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Checkout;

use Hyva\Checkout\Model\Layout as ObsoleteCheckoutLayout;
use Hyva\Checkout\Model\Layout\AbstractUpdateHandleProcessor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\Page as ResultPage;

class StepLayout
{
    private array $updates;

    /** @var array<string, AbstractUpdateHandleProcessor> $updateHandleProcessors */
    private array $updateHandleProcessors;
    private ObsoleteCheckoutLayout $obsoleteCheckoutLayout;

    public function __construct(
        array $updateHandleProcessors = []
    ) {
        $this->updateHandleProcessors = $updateHandleProcessors;

        // To maintain backwards compatibility
        $this->obsoleteCheckoutLayout = ObjectManager::getInstance()->get(ObsoleteCheckoutLayout::class);
    }

    public function setUpdates(array $updates)
    {
        $this->updates = $updates;
    }

    public function getUpdates(): array
    {
        return $this->updates;
    }

    public function hasUpdates(?string $processor = null): bool
    {
        $updates = $this->getUpdates();
        $handles = $updates[$processor] ?? false;

        if ($handles) {
            return count($handles) !== 0;
        }

        foreach ($updates as $update) {
            if ($update['handle'] ?? null) {
                return true;
            }

            return count(array_column($update, 'handle')) !== 0;
        }

        return false;
    }

    /**
     * @param AbstractUpdateHandleProcessor[]|null $processors
     * @return array<int, string>
     */
    public function getUpdateHandles(?array $processors = null): array
    {
        $handles = [];
        $processors ??= $this->getUpdateHandleProcessors();

        foreach ($this->getUpdates() as $processor => $config) {
            if (isset($processors[$processor])) {
                $handles = [...$handles, ...$processors[$processor]->processToArray($config)];
            }
        }

        return $handles;
    }

    public function applyUpdateHandlesToPage(ResultPage $page, ?array $processors = null): ResultPage
    {
        $page->getLayout()->getUpdate()->addHandle($this->getUpdateHandles($processors));

        return $page;
    }

    private function getUpdateHandleProcessors(): array
    {
        // To maintain backwards compatibility with any additional update handle processors injected into the deprecated Layout.
        return array_merge($this->obsoleteCheckoutLayout->getUpdateHandleProcessors(), $this->updateHandleProcessors);
    }
}
