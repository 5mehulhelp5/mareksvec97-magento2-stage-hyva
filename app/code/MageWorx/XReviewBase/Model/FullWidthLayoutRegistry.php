<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

class FullWidthLayoutRegistry
{
    /**
     * @var bool
     */
    protected $result = false;

    /**
     * @param bool $result
     */
    public function setIsFullWidthLayout(bool $result)
    {
        $this->result = $result;
    }

    /**
     * @return bool
     */
    public function getIsFullWidthLayout()
    {
        return $this->result;
    }
}
