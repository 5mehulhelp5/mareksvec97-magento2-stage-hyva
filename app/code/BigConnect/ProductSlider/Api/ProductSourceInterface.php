<?php

declare(strict_types=1);

namespace BigConnect\ProductSlider\Api;

interface ProductSourceInterface
{
    public function getCode(): string;

    public function getLabel(): string;

    /**
     * @param array $config
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getItems(array $config): array;
}
