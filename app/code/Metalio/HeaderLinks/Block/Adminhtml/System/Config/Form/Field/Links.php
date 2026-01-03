<?php
declare(strict_types=1);

namespace Metalio\HeaderLinks\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Links extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('label', ['label' => __('Label'), 'class' => 'required-entry']);
        $this->addColumn('url', ['label' => __('URL / Path'), 'class' => 'required-entry']);
        $this->addColumn('sort', ['label' => __('Sort'), 'class' => 'validate-number', 'style' => 'width:70px']);
        $this->addColumn('new_tab', ['label' => __('New tab (1/0)'), 'style' => 'width:90px']);
        $this->addColumn('css', ['label' => __('CSS class')]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add link');
    }
}
