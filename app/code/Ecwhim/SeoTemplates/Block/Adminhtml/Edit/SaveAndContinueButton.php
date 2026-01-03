<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Block\Adminhtml\Edit;

class SaveAndContinueButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Save and Continue Edit'),
            'class'      => 'save',
            'on_click'   => '',
            'sort_order' => 40
        ];
    }
}
