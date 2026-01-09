<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration\Image;

use BigConnect\Inspiration\Model\ImageUploader;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class Upload extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_Inspiration::manage';

    private ImageUploader $imageUploader;
    private JsonFactory $resultJsonFactory;

    public function __construct(
        Action\Context $context,
        ImageUploader $imageUploader,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            $result = $this->imageUploader->saveFileToTmpDir('image');
        } catch (\Exception $exception) {
            $result = ['error' => $exception->getMessage(), 'errorcode' => $exception->getCode()];
        }

        return $resultJson->setData($result);
    }
}
