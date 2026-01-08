<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CategoryTreeChooser extends Field
{
    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setType('hidden');
        $element->addClass('category-tree-chooser-input');

        $containerId = $element->getHtmlId() . '_chooser';
        $countId = $element->getHtmlId() . '_count';
        $clearId = $element->getHtmlId() . '_clear';

        $storeParam = (string) $this->getRequest()->getParam('store');
        $urlParams = $storeParam !== '' ? ['store' => $storeParam] : [];

        $config = [
            'endpointUrl' => $this->getUrl('hyvastarter/categoryTree/index', $urlParams),
            'inputSelector' => '#' . $element->getHtmlId(),
            'countSelector' => '#' . $countId,
            'clearSelector' => '#' . $clearId,
        ];

        $html = $element->getElementHtml();
        $html .= '<div class="category-tree-chooser" id="' . $containerId . '" data-mage-init=\'';
        $html .= $this->escapeHtmlAttr(json_encode(['bigconnectCategoryTreeChooser' => $config]));
        $html .= '\'>';
        $html .= '<div class="category-tree-chooser__controls" style="margin-bottom:10px;">';
        $html .= '<strong id="' . $countId . '">Selected: 0</strong>';
        $html .= ' <button type="button" class="action-default" id="' . $clearId . '">Clear selection</button>';
        $html .= '</div>';
        $html .= '<div class="category-tree-chooser__tree"></div>';
        $html .= '</div>';

        return $html;
    }
}
