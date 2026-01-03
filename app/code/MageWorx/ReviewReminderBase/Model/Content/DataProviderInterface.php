<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content;

interface DataProviderInterface
{
    /**
     * @return DataContainerInterface[]
     */
    public function getRemindersData(): array;
}
