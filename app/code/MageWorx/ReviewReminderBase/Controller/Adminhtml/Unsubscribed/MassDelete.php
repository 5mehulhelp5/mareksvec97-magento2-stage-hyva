<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Unsubscribed;

use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterface;

class MassDelete extends MassAction
{
    /**
     * @param UnsubscribedInterface $unsubscribed
     * @return $this
     */
    protected function massAction(UnsubscribedInterface $unsubscribed)
    {
        $this->unsubscribedRepository->delete($unsubscribed);

        return $this;
    }
}
