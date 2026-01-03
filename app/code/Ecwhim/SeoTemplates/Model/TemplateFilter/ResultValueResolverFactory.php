<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter;

use Ecwhim\SeoTemplates\Model\TemplateFilter\ResultValueResolver\DefaultResolver;
use Ecwhim\SeoTemplates\Model\TemplateFilter\ResultValueResolver\ResultValueResolverInterface;

class ResultValueResolverFactory
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
     * ResultValueResolverFactory constructor.
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
     * @param string $type
     * @return ResultValueResolverInterface
     */
    public function get(string $type): ResultValueResolverInterface
    {
        if (empty($this->resolvers[$type])) {
            $this->resolvers[$type] = DefaultResolver::class;
        }

        $resolver = $this->objectManager->get($this->resolvers[$type]);

        if (!$resolver instanceof ResultValueResolverInterface) {
            throw new \InvalidArgumentException(
                get_class($resolver) . ' isn\'t instance of ' . ResultValueResolverInterface::class . '.'
            );
        }

        return $resolver;
    }
}
