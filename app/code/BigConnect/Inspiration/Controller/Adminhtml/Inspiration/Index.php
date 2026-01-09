<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_Inspiration::inspiration';

    private PageFactory $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('BigConnect_Inspiration::inspiration');
        $resultPage->getConfig()->getTitle()->prepend(__('Inspirations'));

        return $resultPage;
    }
}
