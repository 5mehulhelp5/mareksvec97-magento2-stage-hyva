<?php
namespace BigConnect\Inspiration\Controller\Adminhtml\Inspiration;

use BigConnect\Inspiration\Model\ImageUploader;
use BigConnect\Inspiration\Model\InspirationFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use BigConnect\Inspiration\Model\Inspiration;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'BigConnect_Inspiration::manage';

    private InspirationFactory $inspirationFactory;
    private ImageUploader $imageUploader;

    public function __construct(
        Action\Context $context,
        InspirationFactory $inspirationFactory,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->inspirationFactory = $inspirationFactory;
        $this->imageUploader = $imageUploader;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data = $data['data'] ?? $data;
        if (!$data) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('entity_id');
        $model = $this->inspirationFactory->create();
        if ($id) {
            $model->load($id);
        }

        try {
            if (isset($data['image'][0]['name'])) {
                $imageName = $data['image'][0]['name'];
                if (isset($data['image'][0]['tmp_name'])) {
                    $imageName = $this->imageUploader->moveFileFromTmp($imageName);
                } elseif (isset($data['image'][0]['file'])) {
                    $imageName = $data['image'][0]['file'];
                }
                $data['image'] = ltrim($imageName, '/');
            } else {
                unset($data['image']);
            }

            $data['position'] = isset($data['position']) ? (int)$data['position'] : 0;
            $data['rating'] = isset($data['rating']) ? (int)$data['rating'] : 5;
            $data['store_id'] = isset($data['store_id']) ? (int)$data['store_id'] : 0;
            $data['product_id'] = isset($data['product_id']) ? (int)$data['product_id'] : 0;
            $data['status'] = $data['status'] ?? Inspiration::STATUS_APPROVED;

            $model->addData($data);
            $model->save();

            $this->messageManager->addSuccessMessage(__('The inspiration has been saved.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the inspiration.'));
        }

        $this->_getSession()->setFormData($data);

        return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['entity_id' => $id]);
    }
}
