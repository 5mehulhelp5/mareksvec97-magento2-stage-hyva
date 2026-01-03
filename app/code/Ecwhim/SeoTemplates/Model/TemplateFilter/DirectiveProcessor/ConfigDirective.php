<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\TemplateFilter\DirectiveProcessor;

class ConfigDirective implements DirectiveProcessorInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Model\Source\ConfigVariables
     */
    private $configVariables;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigDirective constructor.
     *
     * @param \Ecwhim\SeoTemplates\Model\Source\ConfigVariables $configVariables
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Ecwhim\SeoTemplates\Model\Source\ConfigVariables $configVariables,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->configVariables = $configVariables;
        $this->scopeConfig     = $scopeConfig;
    }

    /**
     * @param array $construction
     * @param \Ecwhim\SeoTemplates\Model\TemplateFilter $filter
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process(array $construction, \Ecwhim\SeoTemplates\Model\TemplateFilter $filter): string
    {
        $value = '';
        $path  = $this->getPath($construction[1]);

        if ($path && in_array($path, $this->configVariables->getAvailablePaths())) {
            $value = (string)$this->scopeConfig->getValue(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $filter->getStoreId()
            );
        }

        return $value ? trim($value) : '';
    }

    /**
     * @inheritDoc
     */
    public function getRegularExpression(): string
    {
        return '/{{config\s*(.*?)}}/si';
    }

    /**
     * @param string $value
     * @return string
     */
    private function getPath(string $value): string
    {
        $data = explode('=', $value);

        if (count($data) !== 2 || trim($data[0]) !== 'path') {
            return '';
        }

        $path = trim($data[1]);

        if ($path) {
            $path = ($path[0] === '"') ? trim($path, '"') : trim($path, "'");
        }

        return $path;
    }
}
