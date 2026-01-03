<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model;

use Ecwhim\SeoTemplates\Model\CategoryAttributeValueResolver\CategoryAttributeValueResolverInterface;
use Ecwhim\SeoTemplates\Model\CategoryAttributeValueResolver\DefaultResolver;

class CategoryAttributeValueResolverFactory
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
     * CategoryAttributeValueResolverFactory constructor.
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
     * @return CategoryAttributeValueResolverInterface
     */
    public function get(string $attributeCode): CategoryAttributeValueResolverInterface
    {
        if (empty($this->resolvers[$attributeCode])) {
            $this->resolvers[$attributeCode] = DefaultResolver::class;
        }

        $resolver = $this->objectManager->get($this->resolvers[$attributeCode]);

        if (!$resolver instanceof CategoryAttributeValueResolverInterface) {
            throw new \InvalidArgumentException(
                get_class($resolver) . ' isn\'t instance of ' . CategoryAttributeValueResolverInterface::class . '.'
            );
        }

        return $resolver;
    }
}
