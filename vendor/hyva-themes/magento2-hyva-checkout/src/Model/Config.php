<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Hyva\Checkout\Exception\CheckoutNotFoundException;
use Hyva\Checkout\Model\Config\Reader;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigGeneral;
use Hyva\Checkout\Model\CustomCondition\IsDevice as IsDeviceCustomCondition;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\SerializerInterface;

class Config extends Data
{
    public const CACHE_KEY = 'hyva_checkout_config_cache';

    protected IsDeviceCustomCondition $customConditionIsDevice;
    protected SystemConfigGeneral $systemConfigGeneral;

    public function __construct(
        Reader $reader,
        CacheInterface $cache,
        SystemConfigGeneral $systemConfigGeneral,
        IsDeviceCustomCondition $customConditionIsDevice,
        ?SerializerInterface $serializer = null,
        string $cacheId = self::CACHE_KEY
    ) {
        parent::__construct($reader, $cache, $cacheId, $serializer);

        $this->customConditionIsDevice = $customConditionIsDevice;
        $this->systemConfigGeneral = $systemConfigGeneral;
    }

    /**
     * @return array<string, array{
     *   name: string,
     *   label: string,
     *   parent: string|null,
     *   steps: array{
     *     string: array{
     *       name: string,
     *       label: string,
     *       updates: array,
     *       conditions: array|null,
     *       events: array,
     *       position: int
     *     }
     *   }
     * }>
     * @throws NotFoundException
     */
    public function getList(?array $checkouts = null): array
    {
        $config = $this->get('checkouts');

        if ($checkouts !== null) {
            $config = array_filter($config, static function ($key, $value) use ($checkouts) {
                return in_array($value, $checkouts, true);
            }, ARRAY_FILTER_USE_BOTH);
        }

        if (empty($config)) {
            throw new NotFoundException(__('None of the given checkout types were found'));
        }

        return $config;
    }

    /**
     * @return null|array{
     *   name: string,
     *   label: string,
     *   parent: string|null,
     *   steps: array{
     *     string: array{
     *       name: string,
     *       label: string,
     *       updates: array,
     *       conditions: array|null,
     *       events: array,
     *       position: int
     *     }
     *   }
     */
    public function getDataByPath(array $path): ?array
    {
        return $this->get('checkouts/' . implode('/', $path));
    }

    public function isHyvaCheckout(string $namespace): bool
    {
        return $this->getDataByPath([$namespace]) !== null;
    }

    /**
     * @throws CheckoutNotFoundException
     */
    public function getActiveCheckoutData(): array
    {
        $namespace = $this->getActiveCheckoutNamespace();
        $checkout = $this->getDataByPath([$namespace]);

        if ($checkout) {
            return $checkout;
        }

        throw new CheckoutNotFoundException('Checkout "' . $namespace . '" could not be found or does not exist');
    }

    public function getActiveCheckoutNamespace(): string
    {
        $namespace = $this->systemConfigGeneral->getCheckout();

        if ($this->customConditionIsDevice->onMobile() && $this->systemConfigGeneral->hasMobileCheckout()) {
            return $this->systemConfigGeneral->getMobileCheckout();
        }

        return $namespace;
    }
}
