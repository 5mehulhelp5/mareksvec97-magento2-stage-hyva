<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Reminder;

use Magento\Framework\Model\AbstractModel;

class Template extends AbstractModel
{
    const TEMPLATE_ID = 'template_id';
    const IDENTIFIER  = 'identifier';
    const TITLE       = 'title';
    const CONTENT     = 'content';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_reviewreminderbase_reminder_template';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'reminder_template';
}
