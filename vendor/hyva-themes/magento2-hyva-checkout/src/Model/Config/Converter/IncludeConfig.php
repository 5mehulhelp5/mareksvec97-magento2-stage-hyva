<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config\Converter;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

abstract class IncludeConfig extends DataObject
{
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_data = array_flip($this->_data);
        $this->activate();

        parent::__construct($this->getData());
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws LocalizedException
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'can') === 0) {
            return $this->canApply(lcfirst(substr($method, 3)));
        }

        return parent::__call($method, $args);
    }

    /**
     * @param $key
     * @param bool $value
     * @return IncludeConfig
     */
    public function setData($key, $value = true): IncludeConfig
    {
        if ($key && isset($this->_data[$key])) {
            $this->_data[$key] = true;
        } else {
            $this->_data = array_map(static function () {
                return true;
            }, $this->_data);
        }

        return $this;
    }

    /**
     * @param $key
     * @param bool $default
     * @return bool
     */
    public function canApply($key, bool $default = true): bool
    {
        $data = $this->getData($key, $args[0] ?? null);
        return is_bool($data) ? $data : $default;
    }

    /**
     * Deactivate one or all data property (-s).
     *
     * @param string|null $key
     * @return $this
     */
    public function deactivate(?string $key = null): IncludeConfig
    {
        return $this->setData($key, false);
    }

    /**
     * Activate one or all data property (-s).
     *
     * @param string|null $key
     * @return $this
     */
    public function activate(?string $key = null): IncludeConfig
    {
        return $this->setData($key);
    }
}
