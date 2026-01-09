<?php
namespace BigConnect\Inspiration\Block\Adminhtml\Inspiration\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('bigconnect_inspiration/inspiration/index')),
            'class' => 'back',
            'sort_order' => 10,
        ];
    }
}
