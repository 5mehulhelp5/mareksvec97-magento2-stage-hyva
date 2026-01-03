<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\ViewModel\Checkout;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigFixesWorkarounds;
use Magento\Customer\Api\Data\AddressInterfaceFactory as CustomerAddressInterfaceFactory;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Customer\Block\Address\Renderer\RendererInterface;
use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Customer\Model\Address\Mapper as CustomerAddressMapper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AddressRenderer implements ArgumentInterface
{
    public const SHIPPING_RENDERER = 'hyva_checkout_shipping';
    public const BILLING_RENDERER = 'hyva_checkout_billing';

    protected SessionCheckout $sessionCheckout;
    protected CustomerAddressInterfaceFactory $customerAddressFactory;
    protected CustomerAddressHelper $customerAddressHelper;
    protected CustomerAddressMapper $customerAddressMapper;

    private $systemConfigFixesWorkarounds;

    public function __construct(
        SessionCheckout $sessionCheckout,
        CustomerAddressHelper $customerAddressHelper,
        CustomerAddressMapper $customerAddressMapper,
        ?SystemConfigFixesWorkarounds $systemConfigFixesWorkarounds = null
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->customerAddressHelper = $customerAddressHelper;
        $this->customerAddressMapper = $customerAddressMapper;

        $this->systemConfigFixesWorkarounds = $systemConfigFixesWorkarounds
            ?: ObjectManager::getInstance()->create(SystemConfigFixesWorkarounds::class);
    }

    public function canRenderShippingAddress(): bool
    {
        try {
            return $this->sessionCheckout->getQuote()->getShippingAddress()->validate() === true;
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return false;
        }
    }

    public function renderShippingAddress(string $code = self::SHIPPING_RENDERER): string
    {
        try {
            $addressShipping = $this->sessionCheckout->getQuote()->getShippingAddress();
            return $this->renderCustomerAddress($addressShipping->exportCustomerAddress(), $code);
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return __('%1 address cannot be shown due to a technical malfunction.', 'Shipping')->render();
        }
    }

    public function canRenderBillingAddress(): bool
    {
        return $this->systemConfigFixesWorkarounds->canRenderBillingAddress();
    }

    public function renderBillingAddress(string $code = self::BILLING_RENDERER): string
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            $addressShipping = $quote->getShippingAddress();
            $addressBilling = $addressShipping->getSameAsBilling() && ! $quote->isVirtual() ? $addressShipping : $quote->getBillingAddress();

            if ($addressBilling) {
                return $this->renderCustomerAddress($addressBilling->exportCustomerAddress(), $code);
            }

            throw new NotFoundException(__('%1 address could not be found.', 'Billing'));
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return __('%1 address cannot be shown due to a technical malfunction.', 'Billing')->render();
        }
    }

    public function getFormatTypeRenderer(string $code): ?RendererInterface
    {
        return $this->customerAddressHelper->getFormatTypeRenderer($code);
    }

    public function renderCustomerAddress(CustomerAddressInterface $address, string $code = 'html'): string
    {
        return $this->render($this->customerAddressMapper->toFlatArray($address), $code);
    }

    public function render(array $attributes, string $code): string
    {
        try {
            $renderer = $this->getFormatTypeRenderer($code);

            if ($renderer === null) {
                throw new LocalizedException(__('Renderer by code %1 does not exist', [$code]));
            }

            $html = $this->getFormatTypeRenderer($code)->renderArray($attributes);
            return preg_replace('/^\s*(?:<br\s*\/?>\s*)*/i', '', $html);
        } catch (LocalizedException $exception) {
            return __('Address cannot be shown due to a technical malfunction.')->render();
        }
    }
}
