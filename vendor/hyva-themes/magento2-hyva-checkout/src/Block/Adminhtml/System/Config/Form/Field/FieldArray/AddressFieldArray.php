<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Block\Adminhtml\System\Config\Form\Field\FieldArray;

use Exception;
use Hyva\Checkout\Block\Adminhtml\Element\FieldArray\TypeRenderer;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\BlockFactory;

class AddressFieldArray extends AbstractFieldArray
{
    protected BlockFactory $blockFactory;
    protected Json $serializer;

    public function __construct(
        Context $context,
        BlockFactory $blockFactory,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->blockFactory = $blockFactory;
        $this->serializer = $serializer;
    }

    /**
     * Prepare to render.
     */
    public function _prepareToRender()
    {
        $elementBlockClass = TypeRenderer::class;

        $this->addColumn('sort_order', [
            'label' => __('Sort order'),
        ]);

        $this->addColumn('attribute_code', [
            'label' => __('Attribute'),
            'style' => 'width: 180px'
        ]);

        $this->addColumn('enabled', [
            'label' => __('Enabled'),
            'renderer' => $this->blockFactory->createBlock($elementBlockClass)->setElementType('checkbox')
        ]);

        $this->addColumn('required', [
            'label' => __('Required'),
            'renderer' => $this->blockFactory->createBlock($elementBlockClass)->setElementType('checkbox')
        ]);

        $this->addColumn('auto_save', [
            'label' => __('Auto Save'),
            'renderer' => $this->blockFactory->createBlock($elementBlockClass)->setElementType('checkbox')
        ]);

        $this->addColumn('length', [
            'label' => __('Length'),
            'renderer' => $this->blockFactory->createBlock($elementBlockClass)
                ->setElementType('select')
                ->setElementOptions([
                    __('25%'),
                    __('50%'),
                    __('75%'),
                    __('100%')
                ]),
            'style' => 'width: 75px'
        ]);

        /**
         * @deprecated we don't support any custom CSS since the Tailwind JIT
         *             compiler will not be aware of the class.
         */
//        $this->addColumn('custom_css', [
//            'label' => __('Custom CSS'),
//            'style' => 'width: 100px'
//        ]);

        /**
         * @deprecated default values are determined by the EAV attribute itself.
         *             this column will maybe return in a future version since we
         *             might also add non-EAV attributes.
         */
//        $this->addColumn('default_value', [
//            'label' => __('Default value'),
//            'style' => 'width: 100px'
//        ]);

        $this->addColumn('tool_tip', [
            'label' => __('Tooltip'),
            'style' => 'width: 180px'
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add field');
    }

    public function render(AbstractElement $element): string
    {
        if (! empty($element->getValue()) && ! is_array($element->getValue())) {
            $element->setValue($this->deserialize($element->getValue()));
        }

        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        $html = $this->_renderValue($element);

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderHint($element);
        return $this->_decorateRowHtml($element, $html);
    }

    public function _renderValue(AbstractElement $element): string
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value" colspan="2">';
            $html .= $this->_getElementHtml($element);
        }
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }

        $html .= '</td>';
        return $html;
    }

    public function deserialize($value)
    {
        try {
            return $this->serializer->unserialize($value);
        } catch (Exception $exception) {
            return false;
        }
    }
}
