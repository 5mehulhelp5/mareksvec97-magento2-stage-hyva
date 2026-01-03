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

/**
 * @deprecated has been replaced with an abstract class.
 * @see AbstractUpdateHandleProcessor
 */
interface UpdateHandleProcessorInterface
{
    /**
     * Apply dynamic update handles.
     *
     * @param ResultPage $page
     * @param string[] $config
     * @return ResultPage
     */
    public function process(ResultPage $page, array $config): ResultPage;
}
