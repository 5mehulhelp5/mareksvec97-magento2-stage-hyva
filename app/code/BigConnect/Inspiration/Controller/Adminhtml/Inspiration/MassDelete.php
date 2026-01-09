<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration;

use BigConnect\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_Inspiration::manage';

    private Filter $filter;
    private CollectionFactory $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $deleted = 0;

        foreach ($collection as $item) {
            $item->delete();
            $deleted++;
        }

        if ($deleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $deleted));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
