<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Hyva\Checkout\Model\Checkout\StepLayout;
use Magento\Framework\View\Result\Page as ResultPage;

/**
 * @deprecated has been replaced with a StepLayout
 * @see StepLayout::applyUpdateHandlesToPage()
 */
class Layout
{
    protected array $updateHandleProcessors;

    public function __construct(
        array $updateHandleProcessors = []
    ) {
        $this->updateHandleProcessors = $updateHandleProcessors;
    }

    /**
     * Assign default and dynamic layout update handles.
     */
    public function applyUpdateHandles(ResultPage $page, array $handleProcessorConfig): ResultPage
    {
        foreach ($handleProcessorConfig as $processor => $config) {
            if (isset($this->updateHandleProcessors[$processor])) {
                $this->updateHandleProcessors[$processor]->process($page, $config);
            }
        }

        return $page;
    }

    /**
     * Added after version 1.1.22 to ensure backwards compatibility for systems still relying on this class.
     */
    public function getUpdateHandleProcessors(): array
    {
        return $this->updateHandleProcessors;
    }
}
