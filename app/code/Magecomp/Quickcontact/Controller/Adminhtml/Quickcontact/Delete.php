<?php
namespace Magecomp\Quickcontact\Controller\Adminhtml\Quickcontact;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Backend\App\Action
{
    protected $_quickcontactFactory;
    protected $request;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\Request\Http $request,
        \Magecomp\Quickcontact\Model\QuickcontactFactory $quickcontactfactory
    ) {
    	$this->request = $request;
		$this->_quickcontactFactory = $quickcontactfactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->request->getParam('id');
        $modal = $this->_quickcontactFactory->create();
        $result = $modal->setId($id);
        $result = $result->delete();
        if($result){
            $this->messageManager->addSuccess( __('Delete Record Successfully !') );
        }
		return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('quickcontact/quickcontact/index');
    }

}
