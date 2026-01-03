<?php
namespace Magecomp\Quickcontact\Controller\Adminhtml\Quickcontact;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magecomp\Quickcontact\Model\ResourceModel\Quickcontact\CollectionFactory;

class Massdelete extends \Magento\Backend\App\Action
{

    protected $_filter;
    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $collectionSize = $collection->getSize();
        $collection->walk('delete');
        $this->messageManager->addSuccessMessage(__('A total of %1 Record(s) have been deleted.', $collectionSize));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('quickcontact/quickcontact/index');
    }

}
