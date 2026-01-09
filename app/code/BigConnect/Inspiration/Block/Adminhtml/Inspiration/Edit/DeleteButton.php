<?php
namespace BigConnect\Inspiration\Block\Adminhtml\Inspiration\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $data = [];
        $id = $this->getId();
        if ($id) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    __('Are you sure you want to delete this record?'),
                    $this->getUrl('bigconnect_inspiration/inspiration/delete', ['entity_id' => $id])
                ),
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
