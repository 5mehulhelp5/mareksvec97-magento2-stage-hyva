<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration;

use BigConnect\Inspiration\Model\InspirationFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_Inspiration::manage';

    private PageFactory $resultPageFactory;
    private InspirationFactory $inspirationFactory;
    private Registry $registry;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        InspirationFactory $inspirationFactory,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->inspirationFactory = $inspirationFactory;
        $this->registry = $registry;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('entity_id');
        $model = $this->inspirationFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This inspiration no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $this->registry->register('bigconnect_inspiration', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('BigConnect_Inspiration::inspiration');
        $resultPage->getConfig()->getTitle()->prepend($id ? __('Edit Inspiration') : __('New Inspiration'));

        return $resultPage;
    }
}
