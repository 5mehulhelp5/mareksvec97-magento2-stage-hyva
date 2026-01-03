<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Block\Adminhtml\Edit;

class ResetButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Reset'),
            'on_click'   => 'location.reload();',
            'class'      => 'reset',
            'sort_order' => 30
        ];
    }
}
