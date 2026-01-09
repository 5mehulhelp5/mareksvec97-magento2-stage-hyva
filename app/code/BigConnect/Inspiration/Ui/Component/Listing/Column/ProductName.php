<?php
namespace BigConnect\Inspiration\Ui\Component\Listing\Column;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductName extends Column
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $productRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->productRepository = $productRepository;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as & $item) {
            $item[$this->getData('name')] = $this->getProductLabel((int)($item['product_id'] ?? 0));
        }

        return $dataSource;
    }

    private function getProductLabel(int $productId): string
    {
        if (!$productId) {
            return (string)__('N/A');
        }

        try {
            $product = $this->productRepository->getById($productId);
            $sku = $product->getSku();
            return sprintf('%s (%s)', $product->getName(), $sku);
        } catch (NoSuchEntityException $exception) {
            return (string)__('N/A');
        }
    }
}
