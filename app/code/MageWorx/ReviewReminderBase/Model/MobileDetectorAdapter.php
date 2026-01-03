<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\ReviewReminderBase\Api\MobileDetectorAdapterInterface;

class MobileDetectorAdapter implements MobileDetectorAdapterInterface
{
    /**
     * @var bool|null
     */
    protected $isMobile;

    /**
     * @var bool|null
     */
    protected $deviceDetected;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $httpHeader;

    /**
     * @var DeviceDetectorFactory
     */
    protected $deviceDetectorFactory;

    /**
     * MobileDetectorAdapter constructor.
     *
     * @param DeviceDetectorFactory $deviceDetectorFactory
     * @param \Magento\Framework\HTTP\Header $httpHeader
     */
    public function __construct(
        DeviceDetectorFactory $deviceDetectorFactory,
        \Magento\Framework\HTTP\Header $httpHeader
    ) {
        $this->deviceDetectorFactory = $deviceDetectorFactory;
        $this->httpHeader            = $httpHeader;
    }

    /**
     * @return bool|null
     * @throws LocalizedException
     */
    public function isMobile(): ?bool
    {
        $this->detectDevice();

        return $this->isMobile;
    }

    /**
     * Detecting mobile device
     *
     * @return void
     * @throws LocalizedException
     */
    protected function detectDevice(): void
    {
        if (!$this->deviceDetected) {

            /** @var \DeviceDetector\DeviceDetector|null $deviceDetector */
            $deviceDetector = $this->deviceDetectorFactory->create(['userAgent' => $this->getUserAgent()]);

            if ($deviceDetector) {
                $deviceDetector->discardBotInformation();
                $deviceDetector->parse();

                $this->isMobile = $deviceDetector->isMobile();
            }

            $this->deviceDetected = true;
        }
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    protected function getUserAgent(): string
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();

        if (!is_string($userAgent)) {

            if (is_array($userAgent)) {
                $userAgent = implode(' ', $userAgent);
            } elseif (is_object($userAgent)) {
                /** @var object $userAgent */
                $userAgent = $userAgent->__toString();
            } else {
                throw new LocalizedException(__('Unable to detect user agent.'));
            }
        }

        return $userAgent;
    }
}
