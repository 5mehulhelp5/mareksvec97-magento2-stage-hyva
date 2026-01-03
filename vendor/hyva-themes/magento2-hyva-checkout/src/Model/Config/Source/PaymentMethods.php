<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\Checkout\Model\Config\Source;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Api\PaymentMethodListInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class PaymentMethods implements OptionSourceInterface
{
    private AppState $appState;
    private PaymentMethodListInterface $paymentMethodList;
    private StoreManagerInterface $storeManager;
    private LoggerInterface $logger;

    public function __construct(
        AppState $appState,
        PaymentMethodListInterface $paymentMethodList,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->paymentMethodList = $paymentMethodList;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * This source model is essentially a replica of the B2B payment methods one, except with added exception handling.
     *
     * @see \Magento\CompanyPayment\Model\Source\PaymentMethod::toOptionArray()
     * @return array
     */
    public function toOptionArray(): array
    {
        $paymentMethods = [];
        $storeId = 0;

        try {
            if ($this->appState->getAreaCode() === Area::AREA_FRONTEND) {
                $storeId = $this->storeManager->getStore()->getId();
            }
        } catch (LocalizedException | NoSuchEntityException $e) {
            $this->logger->error(sprintf('Error occurred while compiling payment method list: %s', $e->getMessage()));
        }

        $paymentMethodList = $this->paymentMethodList->getList($storeId);
        usort(
            $paymentMethodList,
            function ($comparedObject, $nextObject) {
                $diff = strcmp($comparedObject->getTitle(), $nextObject->getTitle());
                return ($diff > 0) ? 1 : -1;
            }
        );

        $paymentMethodNames = array_map(
            function ($paymentMethod) {
                return $paymentMethod->getTitle();
            },
            $paymentMethodList
        );

        $duplicatedMethodNames = array_unique(
            array_diff_assoc($paymentMethodNames, array_unique($paymentMethodNames))
        );

        foreach ($paymentMethodList as $method) {
            if ($method->getCode() && $method->getTitle()) {
                $label = $method->getTitle();

                if (in_array($method->getTitle(), $duplicatedMethodNames)) {
                    $label .= ' ' . $method->getCode();
                }

                if (!$method->getIsActive()) {
                    $label .= __(' (disabled)');
                }

                $paymentMethods[] = [
                    'value' => $method->getCode(),
                    'label' => $label,
                ];
            }
        }

        return $paymentMethods;
    }
}
