<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

/**
 * @deprecated has been replaced with a abstract class for future extensibility purposes.
 * @see AbstractMethodMetaData
 */
interface MethodMetaDataInterface
{
    public const ICON = 'icon';
    public const SUBTITLE = 'subtitle';

    /**
     * Get method object.
     *
     * @return object
     */
    public function getMethod(): object;

    public function canRenderIcon(): bool;

    public function renderIcon(): string;

    public function hasSubTitle(): bool;

    public function getSubTitle(): string;
}
