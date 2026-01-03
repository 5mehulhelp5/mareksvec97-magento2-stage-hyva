<?php
declare(strict_types=1);

namespace BigConnect\FmePricecalcPatch\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use FME\Pricecalculator\Model\Pricecalculator as PcModel;

class Index extends Action implements CsrfAwareActionInterface
{
    public function __construct(
        Context $context,
        private JsonFactory $resultJsonFactory,
        private ProductRepositoryInterface $productRepository,
        private PcModel $pcModel
    ) {
        parent::__construct($context);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException { return null; }
    public function validateForCsrf(RequestInterface $request): ?bool { return true; }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        try {
            $payload = json_decode($this->getRequest()->getContent(), true) ?: [];
            $id = (int)($payload['product_id'] ?? 0);
            if (!$id) {
                return $result->setData(['success' => false, 'message' => 'Missing product_id']);
            }

            $product  = $this->productRepository->getById($id, false, null, true);
            $priceExcl = (float)$product->getFinalPrice();

            $pc = $this->pcModel->getPcData($id);
            $pcExists = (bool)$pc;

            return $result->setData([
                'success' => true,
                'price'   => $priceExcl,
                'pc'      => $pcExists ? [
                    // Pozor: vraciame dáta AJ KEĎ enable == 0, aby si vedel použiť unit price z child-a
                    'enabled'       => (int)$pc->getPcEnable() === 1,
                    'unit_price'    => (string)$pc->getPcUnitPrice(),   // môže byť "0,15"
                    'input_unit'    => (string)$pc->getPcInputUnits(),
                    'output_unit'   => (string)$pc->getPcOutputUnits(),
                    'measure_by'    => (string)$pc->getPcMeasureBy(),
                    'discount_type' => (string)$pc->getPcDiscountType(),
                    'size_min'      => (string)$pc->getPcSizeMin(),
                    'size_max'      => (string)$pc->getPcSizeMax(),
                    'disc_min'      => (string)$pc->getPcDiscountMin(),
                    'disc_max'      => (string)$pc->getPcDiscountMax(),
                ] : null,
            ]);
        } catch (\Throwable $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
