<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image;

class SvgImage extends Image
{
    protected function _getAllowedExtensions(): array
    {
        return ['svg'];
    }
}
