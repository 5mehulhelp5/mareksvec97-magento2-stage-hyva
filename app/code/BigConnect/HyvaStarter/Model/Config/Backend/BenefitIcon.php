<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image;

class BenefitIcon extends Image
{
    protected function _getAllowedExtensions(): array
    {
        return ['svg', 'png', 'jpg', 'jpeg', 'webp'];
    }
}
