<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Reminder\Source;

use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Template\Collection;
use MageWorx\ReviewReminderBase\Model\ResourceModel\Reminder\Template\CollectionFactory;
use MageWorx\ReviewReminderBase\Model\Reminder\Template as ReminderTemplate;
use MageWorx\ReviewReminderBase\Model\Source;

class Template extends Source
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Template constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            /** @var Collection $collection */
            $collection = $this->collectionFactory->create();

            $collection->addFieldToSelect([ReminderTemplate::IDENTIFIER, ReminderTemplate::TITLE]);

            $templates = $collection->getData();
            $options   = [];

            foreach ($templates as $template) {
                $options[] = [
                    'value' => $template[ReminderTemplate::IDENTIFIER],
                    'label' => $template[ReminderTemplate::TITLE]
                ];
            }

            $this->options = $options;
        }

        return $this->options ?? [];
    }
}
