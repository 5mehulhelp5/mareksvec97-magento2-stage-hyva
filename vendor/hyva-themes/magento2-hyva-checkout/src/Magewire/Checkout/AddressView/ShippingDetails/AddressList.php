<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire\Checkout\AddressView\ShippingDetails;

use Hyva\Checkout\Exception\CheckoutException;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Magewire\Checkout\AddressView\AddressListInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magewirephp\Magewire\Component;
use Psr\Log\LoggerInterface;

class AddressList extends Component implements AddressListInterface
{
    /** @var int|null $activeAddressEntity */
    public ?int $activeAddressEntity = 0;

    protected $listeners = [
        'shipping_address_submitted' => 'refresh',
        'customer_billing_address_saved' => 'refresh',
    ];

    protected $loader = [
        'activeAddressEntity' => 'Switching address'
    ];

    protected CartRepositoryInterface $quoteRepository;
    protected EvaluationResultFactory $evaluationResultFactory;
    protected SessionCheckout $sessionCheckout;
    protected ShippingAddressManagementInterface $shippingAddressManagement;
    protected LoggerInterface $logger;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        SessionCheckout $sessionCheckout,
        ShippingAddressManagementInterface $shippingAddressManagement
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->sessionCheckout = $sessionCheckout;
        $this->shippingAddressManagement = $shippingAddressManagement;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function boot(): void
    {
        $quote = $this->sessionCheckout->getQuote();
        $addressShippingCustomerId = $quote->getShippingAddress()->getCustomerAddressId();

        if ($addressShippingCustomerId !== null) {
            $this->activeAddressEntity = (int) $addressShippingCustomerId;
        }
    }

    public function activateAddress(string $id): bool
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $addressShipping = $quote->getShippingAddress();

            $availableCustomerAddresses = array_values(array_filter($quote->getCustomer()->getAddresses(), function ($address) use ($id) {
                return (int) $address->getId() === (int) $id;
            }));

            if (count($availableCustomerAddresses) === 0) {
                throw new NoSuchEntityException(__('This address does not belong to you. Please try again'));
            }

            try {
                $addressShippingNew = $addressShipping->importCustomerAddressData($availableCustomerAddresses[0]);

                $this->shippingAddressManagement->assign($quote->getId(), $addressShippingNew);
                // Assign new customer address entity as the active one.
                $this->activeAddressEntity = (int) $addressShippingNew->getCustomerAddressId();

                if ($addressShipping->getSameAsBilling()) {
                    $quote->getBillingAddress()->setCustomerAddressId($addressShippingNew->getCustomerAddressId());
                    $this->quoteRepository->save($quote);
                }

                $this->emit('shipping_address_activated', [
                    'id' => $this->activeAddressEntity
                ]);

                return true;
            } catch (NoSuchEntityException $exception) {
                if ($quote->getIsVirtual()) {
                    throw new LocalizedException(
                        __('Shipping address is not allowed on cart: cart contains no items for shipment.')
                    );
                }
            }
        } catch (CheckoutException $exception) {
            $this->dispatchErrorMessage($exception->getMessage());
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage(__('Something went wrong while activating the address.'));
        }

        return false;
    }

    public function updatingActiveAddressEntity(string $id): int
    {
        if ($this->activateAddress($id)) {
            return (int) $id;
        }

        return (int) $this->activeAddressEntity;
    }
}
