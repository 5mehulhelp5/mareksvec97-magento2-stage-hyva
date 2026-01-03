<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

use Ecwhim\SeoTemplates\Model\ProductAttributeValueResolver\ProductAttributeValueResolverInterface;
use Ecwhim\SeoTemplates\Model\ProductAttributeValueResolver\DefaultResolver;

class ProductAttributeValueResolverFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $resolvers;

    /**
     * ProductAttributeValueResolverFactory constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $resolvers
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, array $resolvers = [])
    {
        $this->objectManager = $objectManager;
        $this->resolvers     = $resolvers;
    }

    /**
     * @param string $attributeCode
     * @return ProductAttributeValueResolverInterface
     */
    public function get(string $attributeCode): ProductAttributeValueResolverInterface
    {
        if (empty($this->resolvers[$attributeCode])) {
            $this->resolvers[$attributeCode] = DefaultResolver::class;
        }

        $resolver = $this->objectManager->get($this->resolvers[$attributeCode]);

        if (!$resolver instanceof ProductAttributeValueResolverInterface) {
            throw new \InvalidArgumentException(
                get_class($resolver) . ' isn\'t instance of ' . ProductAttributeValueResolverInterface::class . '.'
            );
        }

        return $resolver;
    }
}
