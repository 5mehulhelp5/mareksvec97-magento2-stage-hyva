<?php

namespace MetaloPro\GateCategoryControl\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class ConfigurableHelper extends AbstractHelper
{
    protected Configurable $configurableType;
    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        Context $context,
        Configurable $configurableType,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->configurableType = $configurableType;
        $this->productRepository = $productRepository;
    }

    /**
     * Get configurable parent product of a simple product
     *
     * @param Product $simpleProduct
     * @return Product|null
     */
    public function getParentConfigurableProduct(Product $simpleProduct): ?Product
    {
        $parentIds = $this->configurableType->getParentIdsByChild($simpleProduct->getId());

        if (!empty($parentIds)) {
            try {
                return $this->productRepository->getById($parentIds[0]);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return null;
            }
        }

        return null;
    }
}
