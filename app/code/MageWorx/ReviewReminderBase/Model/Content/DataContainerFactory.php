<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content;

use Magento\Framework\ObjectManagerInterface as ObjectManager;
use UnexpectedValueException;

class DataContainerFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $map;

    /**
     * Factory constructor
     *
     * @param ObjectManager $objectManager
     * @param array $map
     */
    public function __construct(
        ObjectManager $objectManager,
        array $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->map           = $map;
    }

    /**
     *
     * @param string $param
     * @param array $arguments
     * @return DataContainerInterface
     * @throws UnexpectedValueException
     */
    public function create(string $param, array $arguments = []): DataContainerInterface
    {
        if (isset($this->map[$param])) {
            $instance = $this->objectManager->create($this->map[$param], $arguments);
        } else {
            throw new UnexpectedValueException('Unknown argument in DataContainerFactory');
        }

        if (!$instance instanceof DataContainerInterface) {
            throw new UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of ' .
                '\MageWorx\ReviewReminderBase\Model\Content\DataContainerInterface'
            );
        }

        return $instance;
    }
}
