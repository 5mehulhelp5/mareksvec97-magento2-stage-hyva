<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Block\Adminhtml\Edit;

class SaveButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Save'),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'class'          => 'save primary',
            'sort_order'     => 50
        ];
    }
}
