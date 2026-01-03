<?php

namespace MetaloPro\GateCategoryControl\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use MetaloPro\GateCategoryControl\Helper\ConfigurableHelper;

class MaterialSwitcher extends Template
{
    protected Registry $registry;
    protected ConfigurableHelper $configurableHelper;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        ConfigurableHelper $configurableHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->configurableHelper = $configurableHelper;
    }

    public function getCurrentProduct(): ?Product
    {
        return $this->registry->registry('current_product');
    }

    public function getParentConfigurable(): ?Product
    {
        $product = $this->getCurrentProduct();
        return $this->configurableHelper->getParentConfigurableProduct($product);
    }

    public function getMaterialOptions(): array
    {
        $parent = $this->getParentConfigurable();
        if (!$parent) return [];

        $usedProducts = $parent->getTypeInstance()->getUsedProducts($parent);
        $options = [];

        foreach ($usedProducts as $variant) {
            $options[] = [
                'label' => $variant->getAttributeText('material'),
                'url'   => $variant->getProductUrl(),
                'active' => $variant->getId() == $this->getCurrentProduct()->getId()
            ];
        }

        return $options;
    }
}
