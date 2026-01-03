<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\Source;

class ConfigVariables implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Config\Model\Config\Structure\SearchInterface
     */
    private $configStructure;

    /**
     * @var array
     */
    private $configPaths = [];

    /**
     * @var array|null
     */
    private $configVariables;

    /**
     * ConfigVariables constructor.
     *
     * @param \Magento\Config\Model\Config\Structure\SearchInterface $configStructure
     * @param array $configPaths
     */
    public function __construct(
        \Magento\Config\Model\Config\Structure\SearchInterface $configStructure,
        array $configPaths = []
    ) {
        $this->configStructure = $configStructure;
        $this->configPaths     = $configPaths;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->getConfigVariables() as $configVariableGroup) {
            foreach ($configVariableGroup['elements'] as $element) {
                $result[] = [
                    'value' => '{{config path="' . $element['value'] . '"}}',
                    'label' => $element['label'],
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toGroupedOptionArray(): array
    {
        $result = [];

        foreach ($this->getConfigVariables() as $configVariableGroup) {
            $groupElements = [];

            foreach ($configVariableGroup['elements'] as $element) {
                $groupElements[] = [
                    'value' => '{{config path="' . $element['value'] . '"}}',
                    'label' => $element['label'],
                ];
            }

            $result[] = [
                'value' => $groupElements,
                'label' => $configVariableGroup['label']
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAvailablePaths(): array
    {
        $result = [];

        foreach ($this->configPaths as $groupElements) {
            $result = array_merge($result, array_keys($groupElements));
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getConfigVariables(): array
    {
        if (isset($this->configVariables)) {
            return $this->configVariables;
        }

        foreach ($this->configPaths as $groupPath => $groupElements) {
            $this->configVariables[$groupPath]['label'] = $this->getGroupLabel($groupPath);

            foreach (array_keys($groupElements) as $elementPath) {
                $this->configVariables[$groupPath]['elements'][] = [
                    'value' => $elementPath,
                    'label' => __($this->configStructure->getElementByConfigPath($elementPath)->getLabel()),
                ];
            }
        }

        return $this->configVariables;
    }

    /**
     * @param string $groupPath
     * @return string
     */
    private function getGroupLabel(string $groupPath): string
    {
        $groupPathElements = explode('/', $groupPath);
        $pathParts         = [];
        $labels            = [];

        foreach ($groupPathElements as $groupPathElement) {
            $pathParts[] = $groupPathElement;
            $labels[]    = __($this->configStructure->getElementByConfigPath(implode('/', $pathParts))->getLabel());
        }

        return implode(' / ', $labels);
    }
}
