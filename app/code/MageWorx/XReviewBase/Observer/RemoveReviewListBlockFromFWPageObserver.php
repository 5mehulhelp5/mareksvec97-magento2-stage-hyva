<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Observer;

class RemoveReviewListBlockFromFWPageObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\XReviewBase\Model\ConfigProvider
     */
    protected $configProvider;
    /**
     * @var \MageWorx\XReviewBase\Model\FullWidthLayoutRegistry
     */
    protected $fullWidthLayoutRegistry;

    /**
     * RemoveReviewListBlockFromFWPageObserver constructor.
     *
     * @param \MageWorx\XReviewBase\Model\ConfigProvider $configProvider
     * @param \MageWorx\XReviewBase\Model\FullWidthLayoutRegistry $fullWidthLayoutRegistry
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\ConfigProvider $configProvider,
        \MageWorx\XReviewBase\Model\FullWidthLayoutRegistry $fullWidthLayoutRegistry
    ) {
        $this->configProvider          = $configProvider;
        $this->fullWidthLayoutRegistry = $fullWidthLayoutRegistry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //layout_generate_blocks_before
        $fullActionName = $observer->getFullActionName();

        if ($fullActionName === 'catalog_product_view') {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $observer->getLayout();
            $block  = $layout->getBlock('product.info.product_additional_data.wrapper');
            if ($block) {
                $this->fullWidthLayoutRegistry->setIsFullWidthLayout(true);
                if ($this->configProvider->getReviewListRenderTypeForFullWidthPage() === 'ajax') {
                    $layout->unsetElement('product.info.product_additional_data.wrapper');
                }
            }
        }
    }
}
