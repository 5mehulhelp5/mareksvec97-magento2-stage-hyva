<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TermsConditionsType implements OptionSourceInterface
{
    public const TYPE_LIST = 'list';
    public const TYPE_PAGE = 'page';
    public const TYPE_MESSAGE = 'message';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'List including all enabled Terms &amp; Conditions',
                'value' => self::TYPE_LIST
            ],
            [
                'label' => 'Message including a CMS page',
                'value' => self::TYPE_PAGE
            ],
            [
                'label' => 'Message only',
                'value' => self::TYPE_MESSAGE
            ]
        ];
    }
}
