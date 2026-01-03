<?php

namespace MetaloPro\GateCategoryControl\Block\Material;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use MetaloPro\GateCategoryControl\Helper\ConfigurableHelper;

class SwitcherLink extends AbstractProduct
{
    protected Registry $registry;
    protected ConfigurableHelper $configurableHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        Registry $registry,
        ConfigurableHelper $configurableHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->configurableHelper = $configurableHelper;
        parent::__construct($context, $data);
    }

    public function getParentProduct(): ?Product
    {
        $currentProduct = $this->registry->registry('current_product');

        if ($currentProduct && $currentProduct->getTypeId() === 'simple') {
            return $this->configurableHelper->getParentConfigurableProduct($currentProduct);
        }

        return null;
    }
}
