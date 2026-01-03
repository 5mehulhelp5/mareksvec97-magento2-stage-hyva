<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Layout;

use Magento\Framework\View\Result\Page as ResultPage;

abstract class AbstractUpdateHandleProcessor implements UpdateHandleProcessorInterface
{
    /**
     * Returns a flat array with layout update handles.
     *
     * @return array<int, string>
     */
    abstract public function processToArray(array $config): array;

    /**
     * Returns the page result with all required handles attached.
     */
    public function processToPage(array $config, ResultPage $page): ResultPage
    {
        // Supports the deprecated "process" method for backward compatibility reasons.
        if (method_exists($this, 'process')) {
            $handles = $this->process($page, $config)->getLayout()->getUpdate()->getHandles();
        }

        $page->addHandle([...$handles ?? [], $this->processToArray($config)]);
        return $page;
    }

    /**
     * @deprecated has been replaced with processToPage()
     * @see self::processToPage()
     */
    public function process(ResultPage $page, array $config): ResultPage
    {
        $page->addHandle($this->processToArray($config));

        return $page;
    }
}
