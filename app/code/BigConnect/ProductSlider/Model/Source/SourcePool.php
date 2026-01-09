<?php

declare(strict_types=1);

namespace BigConnect\ProductSlider\Model\Source;

use BigConnect\ProductSlider\Api\ProductSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class SourcePool
{
    /**
     * @var ProductSourceInterface[]
     */
    private array $sources;

    /**
     * @param ProductSourceInterface[] $sources
     */
    public function __construct(array $sources = [])
    {
        foreach ($sources as $source) {
            if (!$source instanceof ProductSourceInterface) {
                throw new LocalizedException(__('Invalid product source configured.'));
            }
        }

        $this->sources = $sources;
    }

    public function get(string $code): ProductSourceInterface
    {
        if (!$this->has($code)) {
            throw new LocalizedException(__('Product source "%1" is not available.', $code));
        }

        return $this->sources[$code];
    }

    public function has(string $code): bool
    {
        return isset($this->sources[$code]);
    }
}
