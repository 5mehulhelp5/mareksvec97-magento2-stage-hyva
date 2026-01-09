<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration;

use BigConnect\Inspiration\Model\Inspiration;
use BigConnect\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;

class MassEnable extends Action
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
        $updated = 0;

        foreach ($collection as $item) {
            $item->setData('status', Inspiration::STATUS_APPROVED);
            $item->save();
            $updated++;
        }

        if ($updated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were enabled.', $updated));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
