<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Layout;

use Hyva\Checkout\Model\CustomConditionFactory;
use Hyva\Checkout\Model\CustomConditionInterface;
use Hyva\Checkout\Model\CustomConditionProcessor;
use Magento\Framework\View\Result\Page as ResultPage;

class CustomUpdateHandleProcessor extends AbstractUpdateHandleProcessor
{
    protected CustomConditionFactory $customConditionFactory;
    protected CustomConditionProcessor $customConditionProcessor;

    /**
     * @param CustomConditionFactory $customConditionFactory
     * @param CustomConditionProcessor $customConditionProcessor
     */
    public function __construct(
        CustomConditionFactory $customConditionFactory,
        CustomConditionProcessor $customConditionProcessor
    ) {
        $this->customConditionFactory = $customConditionFactory;
        $this->customConditionProcessor = $customConditionProcessor;
    }

    public function processToArray(array $config): array
    {
        $handles = [];

        $types = $this->customConditionFactory->produce(
            array_column($config, 'type')
        );

        foreach ($config as $item) {
            if ($this->customConditionProcessor->isApplicable($types[$item['type']], $item['method'])) {
                $handles = [...$handles, $item['handle']];
            }
        }

        return $handles;
    }
}
