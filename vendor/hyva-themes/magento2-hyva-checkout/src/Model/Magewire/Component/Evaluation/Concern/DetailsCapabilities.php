<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Evaluation\Concern;

use Magewirephp\Magewire\Component;

trait DetailsCapabilities
{
    private array $details = [];

    /**
     * Include non-strict details.
     *
     * @param array<string, string|int|float|bool> $data
     *
     * @return static till PHP8.x
     * @doc https://github.com/php/php-src/pull/5062
     */
    public function withDetails(array $data)
    {
        $this->details = $data;

        return $this;
    }

    /**
     * Provides additional detailed arguments that can be used when parsing the main arguments array.
     * Details, in contrast to primary arguments, can be developer-defined and automatically merged
     * to convey component-specific information.
     *
     * Best practise is to define the details always as key "detail" when included into the arguments.
     *
     * @return array<string, string|int|float|bool>
     */
    protected function getDetails(Component $component): array
    {
        return $this->details + [
                'component' => [
                    'id' => $component->id
                ]
            ];
    }
}
