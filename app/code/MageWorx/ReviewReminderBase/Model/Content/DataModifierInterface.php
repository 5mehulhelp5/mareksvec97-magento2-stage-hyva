<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content;

interface DataModifierInterface
{
    /**
     * @param ContainerManagerInterface $containerManager
     * @return void
     */
    public function modify(ContainerManagerInterface $containerManager): void;
}
