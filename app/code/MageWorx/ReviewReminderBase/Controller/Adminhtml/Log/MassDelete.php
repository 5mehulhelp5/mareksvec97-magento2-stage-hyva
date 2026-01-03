<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Controller\Adminhtml\Log;

use MageWorx\ReviewReminderBase\Api\Data\LogRecordInterface;

class MassDelete extends MassAction
{
    /**
     * @param LogRecordInterface $logRecord
     * @return $this
     */
    protected function massAction(LogRecordInterface $logRecord)
    {
        $this->logRecordRepository->delete($logRecord);

        return $this;
    }
}
