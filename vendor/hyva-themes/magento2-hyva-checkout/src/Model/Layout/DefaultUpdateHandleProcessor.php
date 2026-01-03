<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Layout;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\View\Result\Page as ResultPage;

class DefaultUpdateHandleProcessor extends AbstractUpdateHandleProcessor
{
    protected EventManagerInterface $eventManager;

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    public function processToArray(array $config, ?ResultPage $page = null): array
    {
        $handles = array_column($config, 'handle');

        if ($page === null) {
            return $handles;
        }

        return $page->getLayout()->getUpdate()->getHandles();
    }

    public function processToPage(array $config, ResultPage $page): ResultPage
    {
        $handles = $this->processToArray($config);

        foreach ($page->getLayout()->getUpdate()->getHandles() as $handle) {
            $this->eventManager->dispatch('hyva_checkout_layout_process_before_' . $handle, [
                'page' => $page
            ]);
        }

        // Event to optionally apply layout handles
        $this->eventManager->dispatch('hyva_checkout_layout_process_before', [
            'page' => $page
        ]);

        $page->addHandle($handles);
        return $page;
    }
}
