<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\ObjectManagerInterface;

class DeviceDetectorFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * DeviceDetectorFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $arguments
     * @return \DeviceDetector\DeviceDetector|null
     */
    public function create(array $arguments = []): ?\DeviceDetector\DeviceDetector
    {
        if (class_exists('\DeviceDetector\DeviceDetector')) {
            return $this->objectManager->create('\DeviceDetector\DeviceDetector', $arguments);
        }

        return null;
    }
}
