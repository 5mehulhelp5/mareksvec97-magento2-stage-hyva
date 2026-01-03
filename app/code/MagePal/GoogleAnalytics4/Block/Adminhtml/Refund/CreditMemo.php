<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * https://www.magepal.com | support@magepal.com
 */

namespace MagePal\GoogleAnalytics4\Block\Adminhtml\Refund;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use MagePal\GoogleAnalytics4\Helper\Data;
use MagePal\GoogleAnalytics4\Model\Session\Admin\CreditMemo as CreditMemoSession;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

class CreditMemo extends Template
{
    /**
     * EE Helper
     *
     * @var Data
     */
    protected $eeHelper;

    /**
     * @var CreditMemoSession
     */
    protected $creditMemoSession;

    /**
     * @var int
     */
    protected $store_id = null;

    /**
     * @param Context $context
     * @param Data $eeHelper
     * @param CreditMemoSession $creditMemoSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $eeHelper,
        CreditMemoSession $creditMemoSession,
        array $data = []
    ) {
        $this->eeHelper = $eeHelper;
        $this->creditMemoSession = $creditMemoSession;

        $this->store_id = $creditMemoSession->getStoreId();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->eeHelper->isEnabled($this->store_id)
            || !$this->creditMemoSession->getOrderId()
            || $this->store_id != $this->creditMemoSession->getGtmAccountStoreId()
        ) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getDataLayerName()
    {
        return $this->eeHelper->getDataLayerName($this->store_id);
    }

    /**
     * @return null|string
     */
    public function getJsonData()
    {
        $refundJson = [
            'event' => DataLayerEvent::GA4_REFUND_EVENT,
            'ecommerce' => [
                'transaction_id' => $this->creditMemoSession->getIncrementId(),
                'items' => $this->creditMemoSession->getproducts(),
                'currency' => $this->creditMemoSession->getBaseCurrencyCode()
            ]
        ];

        $revenue = $this->creditMemoSession->getAmount();
        if ($revenue) {
            $refundJson['ecommerce']['value'] = $revenue;
        }

        $this->creditMemoSession->clearStorage();

        return json_encode($refundJson);
    }
}
