<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Block\Adminhtml\Element\FieldArray;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;

class TypeRenderer extends AbstractBlock
{
    protected FormElementFactory $formElementFactory;
    protected DataObjectFactory $dataObjectFactory;

    public function __construct(
        Context $context,
        FormElementFactory $formElementFactory,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->formElementFactory = $formElementFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * objectFactory wrapper to receive dull objects with data.
     */
    protected function _getFormMock(array $data = []): DataObject
    {
        return $this->dataObjectFactory->create($data);
    }

    /**
     * elementFactory wrapper to get elements by type, assign data and form dependency.
     */
    protected function _getElement($type = 'checkbox', array $data = [], array $formData = []): AbstractElement
    {
        return $this->formElementFactory
            ->create($type, ['data' => $data])
            ->setForm($this->_getFormMock($formData));
    }

    /**
     * @see \Magento\Framework\View\Element\AbstractBlock::_toHtml()
     */
    protected function _toHtml()
    {
        $data = [
            'html_id' => $this->getInputId(),
            'name' => $this->getInputName(),
            'options' => $this->getElementOptions()
        ];

        /*
         * \Magento\Framework\Data\Form\Element\*
         * Has a dependency where you need form data to be present
         * to get the element name
         */
        $formData = ['html_id_prefix' => '', 'html_id_suffix' => ''];

        $element = $this->_getElement($this->getElementType(), $data, $formData);
        $elementHtml = str_replace(["\r", "\n"], '', $element->getElementHtml());

        if ($this->getElementType() === 'checkbox') {
            $elementHtml = str_replace(
                'value=""',
                'value="<%- ' . $this->getColumnName() . ' %>"',
                $element->getElementHtml()
            );
        }

        if ($this->getElementType() === 'checkbox') {
            $elementHtml = str_replace('checkbox', 'hidden', $elementHtml) . $elementHtml;
        }

        return $elementHtml;
    }
}
