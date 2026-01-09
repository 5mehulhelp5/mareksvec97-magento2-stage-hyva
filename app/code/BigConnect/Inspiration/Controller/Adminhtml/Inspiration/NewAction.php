<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\ForwardFactory;

class NewAction extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_Inspiration::manage';

    private ForwardFactory $resultForwardFactory;

    public function __construct(
        Action\Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
