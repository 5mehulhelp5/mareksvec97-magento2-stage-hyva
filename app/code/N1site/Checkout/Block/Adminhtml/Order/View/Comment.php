<?php

namespace Tvojstyl\Checkout\Block\Adminhtml\Order\View;

class Comment extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Retrieve order's comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->getOrder()->getData('comment');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getComment()) {
            return '';
        }

        return parent::_toHtml();
    }
}