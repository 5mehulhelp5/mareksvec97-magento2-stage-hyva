<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Api;

interface MobileDetectorAdapterInterface
{
    /**
     * @return bool|null
     */
    public function isMobile(): ?bool;
}
