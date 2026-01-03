<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\MethodMetaData;

use Exception;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigDesign;
use Hyva\Theme\ViewModel\SvgIcons;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\View\Asset\File\NotFoundException as SvgNotFoundException;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Psr\Log\LoggerInterface;

class IconRenderer
{
    protected SvgIcons $svgIcons;
    protected Escaper $escaper;
    protected AssetRepository $assetRepository;
    protected LoggerInterface $logger;
    protected SystemConfigDesign $systemConfigDesign;

    public function __construct(
        SvgIcons $svgIcons,
        LoggerInterface $logger,
        ?SystemConfigDesign $systemConfigDesign = null,
        ?Escaper $escaper = null,
        ?AssetRepository $assetRepository = null
    ) {
        $this->svgIcons = $svgIcons;
        $this->logger = $logger;

        $this->systemConfigDesign = $systemConfigDesign
            ?: ObjectManager::getInstance()->get(SystemConfigDesign::class);
        $this->escaper = $escaper
            ?: ObjectManager::getInstance()->get(Escaper::class);
        $this->assetRepository = $assetRepository
            ?: ObjectManager::getInstance()->get(AssetRepository::class);
    }

    public function render(array $logo): string
    {
        $attributes = $logo['attributes'] ?? [];

        if (isset($logo['svg'])) {
            return $this->renderAsSvg($logo['svg'], $attributes);
        }
        if (isset($logo['src'])) {
            return $this->renderAsImage($logo['src'], $attributes);
        }

        return '';
    }

    public function renderAsSvg(string $path, array $attributes = []): string
    {
        $iconWidth  = (int) ($attributes['width'] ?? $this->systemConfigDesign->getUniversalIconWidth());
        $iconHeight = (int) ($attributes['height'] ?? $this->systemConfigDesign->getUniversalIconHeight());

        $attributes = array_map([$this->escaper, 'escapeHtmlAttr'], $attributes);

        try {
            return $this->svgIcons->renderHtml($path, '', $iconWidth, $iconHeight, $attributes);
        } catch (SvgNotFoundException $exception) {
            $this->logger->warning(
                sprintf('No SVG found for %s in %s.', $path, get_class($this)),
                ['exception' => $exception]
            );
        }

        return '';
    }

    public function renderAsImage(string $url, array $attributes = []): string
    {
        try {
            $url = $this->assetRepository->getUrl($url);
        } catch (Exception $exception) {
            return '';
        }

        $html = '<img src="' . $this->escaper->escapeUrl($url) . '"';

        foreach ($attributes as $name => $value) {
            $html .= ' ' . $this->escaper->escapeHtml($name) . '="' . $this->escaper->escapeHtmlAttr($value) . '"';
        }

        $html .= '/>';

        return $html;
    }
}
