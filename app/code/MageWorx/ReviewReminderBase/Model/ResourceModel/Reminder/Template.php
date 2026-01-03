<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageWorx\ReviewReminderBase\Model\Reminder\Template as TemplateModel;

class Template extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            'mageworx_reviewreminderbase_reminder_template',
            TemplateModel::TEMPLATE_ID
        );
    }
}
