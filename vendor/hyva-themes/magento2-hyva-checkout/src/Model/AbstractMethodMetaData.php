<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Hyva\Checkout\Model\MethodMetaData\IconRenderer;
use Hyva\Checkout\Model\MethodMetaData\SubtitleRenderer;
use Magento\Framework\DataObject;

abstract class AbstractMethodMetaData extends DataObject implements MethodMetaDataInterface
{
    private IconRenderer $iconRenderer;
    private SubtitleRenderer $subtitleRenderer;

    protected $method;

    public function __construct(
        IconRenderer $iconRenderer,
        SubtitleRenderer $subtitleRenderer,
        $method,
        array $data = []
    ) {
        $this->iconRenderer = $iconRenderer;
        $this->subtitleRenderer = $subtitleRenderer;
        $this->method = $method;

        parent::__construct($data);
    }

    public function getMethod(): object
    {
        return $this->method;
    }

    public function canRenderIcon(): bool
    {
        return true;
    }

    /**
     * Retrieves the icon and renders it if applicable.
     */
    public function renderIcon(): string
    {
        $icon = $this->getData(self::ICON);

        if (is_array($icon)) {
            return $this->iconRenderer->render($icon);
        }
        if (is_string($icon)) {
            return $this->iconRenderer->renderAsSvg($icon);
        }

        return '';
    }

    public function hasSubTitle(): bool
    {
        return $this->getData(self::SUBTITLE) && is_string($this->getData(self::SUBTITLE));
    }

    /**
     * Retrieves the subtitle and renders it if applicable.
     */
    public function getSubTitle(): string
    {
        $subtitle = $this->getData(self::SUBTITLE) ?? '';

        if (is_string($subtitle) && ! empty($subtitle)) {
            $subtitle = $this->subtitleRenderer->render($subtitle);
        }

        return (string) $subtitle;
    }
}
