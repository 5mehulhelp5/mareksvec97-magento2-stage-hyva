<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config;

use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\CompositeFileIteratorFactory;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\Design\Theme\CustomizationInterfaceFactory;
use Magento\Framework\View\Design\Theme\FlyweightFactory;
use Magento\Framework\View\Design\Theme\ThemeList;
use Magento\Theme\Model\Theme;

class FileResolver implements FileResolverInterface
{
    protected CompositeFileIteratorFactory $iteratorFactory;
    protected Filesystem $filesystem;
    protected ReadInterface $rootDir;
    protected CustomizationInterfaceFactory $themeInfoFactory;
    protected ThemeInterface $theme;
    protected FileResolverInterface $moduleFileResolver;
    protected FlyweightFactory $themeFactory;
    protected DesignInterface $design;
    protected ThemeList $themeList;

    public function __construct(
        FileResolverInterface $moduleFileResolver,
        DesignInterface $design,
        CustomizationInterfaceFactory $customizationFactory,
        Filesystem $filesystem,
        CompositeFileIteratorFactory $iteratorFactory,
        FlyweightFactory $themeFactory,
        ?ThemeList $themeList = null
    ) {
        $this->iteratorFactory = $iteratorFactory;
        $this->filesystem = $filesystem;
        $this->moduleFileResolver = $moduleFileResolver;
        $this->themeInfoFactory = $customizationFactory;
        $this->rootDir = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->themeFactory = $themeFactory;
        $this->design = $design;
        $this->themeList = $themeList ?: ObjectManager::getInstance()->get(ThemeList::class);
    }

    public function get($filename, $scope)
    {
        $configs = $this->moduleFileResolver->get($filename, $scope);

        if ($scope === 'global') {
            $files = [];

            /** @var Theme $theme */
            foreach ($this->themeList->getItems() as $theme) {
                if (in_array($theme->getThemePath(), $this->getExcludedThemes())) {
                    continue;
                }

                $info = $this->themeInfoFactory->create(['theme' => $theme]);
                $file = $info->getThemeFilesPath() . '/etc/' . $filename;

                if ($this->rootDir->isExist($file)) {
                    $files[] = $file;
                }
            }

            $configs = $this->iteratorFactory->create([
                'paths' => array_reverse($files),
                'existingIterator' => $configs
            ]);
        }

        return $configs;
    }

    protected function getExcludedThemes(): array
    {
        return [
            'Magento/backend',
        ];
    }
}
