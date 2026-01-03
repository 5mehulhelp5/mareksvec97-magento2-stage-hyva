<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate;

use Magento\Framework\App\Action\HttpGetActionInterface;

class NewAction extends \Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate implements HttpGetActionInterface
{
    const MENU_ID = 'Ecwhim_SeoTemplates::seo_templates_product_template';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * NewAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(static::MENU_ID);
        $resultPage->getConfig()->getTitle()->prepend(__('Product SEO Templates'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Product Template'));

        return $resultPage;
    }
}
