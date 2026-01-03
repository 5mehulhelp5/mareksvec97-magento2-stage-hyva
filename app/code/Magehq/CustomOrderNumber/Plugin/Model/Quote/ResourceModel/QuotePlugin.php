<?php
/**
 * Magehq
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the mageqh.com license that is
 * available through the world-wide-web at this URL:
 * https://magehq.com/license.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Magehq
 * @package    Magehq_CustomOrderNumber
 * @copyright  Copyright (c) 2022 Magehq (https://magehq.com/)
 * @license    https://magehq.com/license.html
 */
 
namespace Magehq\CustomOrderNumber\Plugin\Model\Quote\ResourceModel;

use Magento\Quote\Model\ResourceModel\Quote;

class QuotePlugin
{
    /**
     * @var \Magehq\CustomOrderNumber\Helper\Generator
     */
    protected $incrementIdGenerator;

    /**
     * QuotePlugin constructor.
     *
     * @param \Magehq\CustomOrderNumber\Helper\Generator $incrementIdGenerator
     */
    public function __construct(
        \Magehq\CustomOrderNumber\Helper\Generator $incrementIdGenerator
    ) {
        $this->incrementIdGenerator = $incrementIdGenerator;
    }

    /**
     * @param Quote $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @return mixed
     */
    public function aroundGetReservedOrderId(Quote $subject, \Closure $proceed, $quote)
    {
        $originalSequence = $proceed($quote);
        // Generate new increment ID
        $incrementId = $this->incrementIdGenerator->generateIncrementId($quote, \Magento\Sales\Model\Order::ENTITY, $originalSequence);
        return $incrementId;
    }
}
