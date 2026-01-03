<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Template;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MageWorx\ReviewReminderBase\Model\Reminder\Template;

class Collection extends AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = Template::TEMPLATE_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_reviewreminderbase_reminder_template_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'reminder_template_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Template::class, \MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Template::class);
    }
}
