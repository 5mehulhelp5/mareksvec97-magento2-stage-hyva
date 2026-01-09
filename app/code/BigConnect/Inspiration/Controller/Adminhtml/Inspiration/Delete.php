<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration;

use BigConnect\Inspiration\Model\InspirationFactory;
use Magento\Backend\App\Action;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_Inspiration::manage';

    private InspirationFactory $inspirationFactory;

    public function __construct(
        Action\Context $context,
        InspirationFactory $inspirationFactory
    ) {
        parent::__construct($context);
        $this->inspirationFactory = $inspirationFactory;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('entity_id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('Unable to find an inspiration to delete.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $model = $this->inspirationFactory->create();
            $model->load($id);
            $model->delete();
            $this->messageManager->addSuccessMessage(__('The inspiration has been deleted.'));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Unable to delete the inspiration.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
