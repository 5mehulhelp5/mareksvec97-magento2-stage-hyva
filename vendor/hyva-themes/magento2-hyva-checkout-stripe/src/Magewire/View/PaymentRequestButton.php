<?php
/*
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\CheckoutStripe\Magewire\View;

use Magento\Checkout\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Escaper;
use Magewirephp\Magewire\Component;
use StripeIntegration\Payments\Api\ServiceInterface;
use StripeIntegration\Payments\Exception\GenericException;
use StripeIntegration\Payments\Helper\InitParams;
use Hyva\CheckoutStripe\Model\Config\DefaultCountryProvider;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Shipping\Model\Shipping;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class PaymentRequestButton extends Component
{

    protected $listeners = [
        'shipping_method_selected' => 'reLoadSettings',
        'payment_method_selected' => 'reLoadSettings',
        'coupon_code_applied' => 'reLoadSettings',
        'coupon_code_revoked' => 'reLoadSettings',
        'shipping_address_saved' => 'reLoadSettings',
        'shipping_address_activated' => 'reLoadSettings',
        'billing_address_saved' => 'reLoadSettings',
        'billing_address_activated' => 'reLoadSettings'
    ];

    private SerializerInterface $serializer;
    private Registry $registry;
    private Session $checkoutSession;
    private ServiceInterface $service;
    private InitParams $initParams;
    private Escaper $escaper;
    private DefaultCountryProvider $defaultCountryProvider;
    private CartRepositoryInterface $quoteRepository;
    private StoreManagerInterface $storeManager;
    private RateRequestFactory $rateRequestFactory;
    private Shipping $shipping;
    private ProductCollectionFactory $productCollectionFactory;


    public array $eceParams = [];
    public array $walletParams = [];
    public array $placeOrderResult = [];
    public array $shippingAddress = [];
    public bool $isEmptyCart = false;
    public ?int $productId = null;
    public array $resolvePayload = [];
    public bool $orderPlaced = false;
    public int $totalAmount = 0;

    public function __construct(
        SerializerInterface $serializer,
        Registry $registry,
        Session $checkoutSession,
        ServiceInterface $service,
        Escaper $escaper,
        InitParams $initParams,
        DefaultCountryProvider $defaultCountryProvider,
        CartRepositoryInterface $quoteRepository,
        StoreManagerInterface $storeManager,
        RateRequestFactory $rateRequestFactory,
        Shipping $shipping,
        ProductCollectionFactory $productCollectionFactory,
        ?string $id = null
    ) {
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        $this->service = $service;
        $this->initParams = $initParams;
        $this->escaper = $escaper;
        $this->defaultCountryProvider = $defaultCountryProvider;
        $this->quoteRepository = $quoteRepository;
        $this->storeManager = $storeManager;
        $this->rateRequestFactory = $rateRequestFactory;
        $this->shipping = $shipping;
        $this->productCollectionFactory = $productCollectionFactory;

        // This component is added to the page multiple times (for the product and the minicart for example),
        // so we need to make sure the ID is unique
        $this->id = $id ?: 'stripe.payment-request-button.' . uniqid();

        $this->productId = $this->getProductId();
    }

    public function getProductId(): ?int
    {
        $product = $this->registry->registry('product');
        return $product ? (int)$product->getId() : null;
    }

    public function loadSettings(string $type, ?int $productId = null): void
    {
        $quote = $this->checkoutSession->getQuote();

        $this->isEmptyCart = $quote->getItemsCount() == 0;

        $this->eceParams = $this->serializer->unserialize(
            $this->service->ece_params($type, $productId)
        );

        //needed for all buttons to be displayed at once (otherwise hidden)
        $this->fixEceParamsOverflow();
        $this->fixEceParamsCountries();
        $this->fixEceParamsShippingRates($type);

        $this->walletParams = $this->serializer->unserialize(
            $this->initParams->getWalletParams()
        );

        //needed for making a selected shipping rate in quote to be first in modal/popup

        $this->refreshRatesKeepingSelection();

        $this->setResolvePayload($this->eceParams['resolvePayload']);
    }

    public function reLoadSettings(): void
    {
        $this->loadSettings('checkout');
        $this->dispatchBrowserEvent('reload-stripe-express-element');
    }

    public function addProductAndPreparePayload(array $formData): void
    {
        $addToCart = true;
        $productId = (int)($formData['product'] ?? 0);
        $quote = $this->checkoutSession->getQuote();

        if ($productId > 0 && $quote && $quote->getId() ) {
            if ($quote->hasProductId($productId)) {
                $addToCart = false;
            }
        }
        //important to check if this item is already in cart or not
        // otherwise it will reset the shipping method from quote
        if($addToCart) {
             $this->service->addtocart($formData);
        }

    }

    /**
     * Ensure the currently selected quote shipping method (e.g. "flatrate_flatrate")
     * is the first item in resolvePayload.shippingRates (and mirrored in eceParams).
     */
    private function reorderShippingRatesBySelectedQuoteMethod(): void
    {
        if (
            empty($this->resolvePayload['shippingRates']) ||
            !is_array($this->resolvePayload['shippingRates'])
        ) {
            return;
        }

        $quote = $this->checkoutSession->getQuote();
        if (!$quote || !$quote->getId() || $quote->isVirtual()) {
            return;
        }

        $selectedMethod = $quote->getShippingAddress()->getShippingMethod();
        if ($selectedMethod === '') {
            return;
        }

        $rates = $this->resolvePayload['shippingRates'];

        $foundIdx = null;
        foreach ($rates as $idx => $rate) {
            $id = (string)($rate['id'] ?? '');
            if ($id === $selectedMethod) {
                $foundIdx = $idx;
                break;
            }
        }

        if ($foundIdx === null) {
            return; // nothing to reorder
        }

        $selectedRate = $rates[$foundIdx];
        unset($rates[$foundIdx]);
        array_unshift($rates, $selectedRate);
        $rates = array_values($rates);

        // Update both resolvePayload and eceParams mirrors.
        $this->resolvePayload['shippingRates'] = $rates;
        if (isset($this->eceParams['resolvePayload']['shippingRates'])) {
            $this->eceParams['resolvePayload']['shippingRates'] = $rates;
        }
    }

    public function refreshRatesKeepingSelection(): void
    {
        $this->reorderShippingRatesBySelectedQuoteMethod();
    }

    public function addProductToCart(array $request, ?string $shipping_id = null): void
    {
        $this->service->addtocart($request, $shipping_id);
    }

    public function estimateCart(array $address, $type = 'product'): void
    {

        $this->shippingAddress = $address;
        $quote = $this->checkoutSession->getQuote();
        if (!$quote || !$quote->getId() || $quote->isVirtual()) {
            return;
        }

        $selectedMethod = $quote->getShippingAddress()->getShippingMethod();

        //we added a plugin to manipulate rate ordering inside this method
        $result = $this->serializer->unserialize(
            $this->service->ece_shipping_address_changed($address, 'product')
        );

        $this->reorderShippingRatesBySelectedQuoteMethod();
        $result['resolvePayload']['shippingRates'] = $this->resolvePayload['shippingRates'];
        $this->setResolvePayload($result['resolvePayload']);
        $quote->getShippingAddress()->setShippingMethod($selectedMethod);

        if($type === 'checkout') {
            $this->emitToRefresh('checkout.shipping.methods');
            $this->emitToRefresh('price-summary.total-segments');
        }

    }

    public function updateShippingRate(string $shippingMethod, string $type = 'product'): void
    {

        //we added a plugin to manipulate rate ordering inside this method
        $result = $this->serializer->unserialize(
            $this->service->ece_shipping_rate_changed($this->shippingAddress, $shippingMethod)
        );

        $this->reorderShippingRatesBySelectedQuoteMethod();
        $result['resolvePayload']['shippingRates'] = $this->resolvePayload['shippingRates'];
        $this->setResolvePayload($result['resolvePayload']);

        if($type === 'checkout') {
            $this->emitToRefresh('checkout.shipping.methods');
            $this->emitToRefresh('price-summary.total-segments');
        }

    }

    public function placeOrder(array $result, string $location): void
    {
        try {
            $this->placeOrderResult = $this->serializer->unserialize(
                $this->service->place_order($result, $location)
            );
            $this->orderPlaced = true;
        } catch (GenericException $exception) {
            if (stripos($exception->getMessage(), 'Authentication Required: ') !== false) {
                $clientSecret = substr($exception->getMessage(), strlen('Authentication Required: '));
                $this->dispatchBrowserEvent('stripe-authenticate-customer', ['clientSecret' => $clientSecret]);
                return;
            }
            $this->placeOrderResult = ['error' => $this->escaper->escapeHtml($exception->getMessage())];
        } catch (\Throwable $exception) {
            $this->placeOrderResult = ['error' => $this->escaper->escapeHtml($exception->getMessage())];
        }
    }

    /**
     * Unifies shipping rate refresh for checkout & product.
     */
    private function fixEceParamsShippingRates(string $type): void
    {
        if ($type === 'checkout' || $type === 'minicart'|| $type === 'cart') {
            $this->refreshCheckoutShippingRates();
            return;
        }

        if ($type === 'product') {
            $this->refreshProductShippingRates();
        }
    }

    /**
     * Checkout context: use quote shipping country when possible.
     */
    private function refreshCheckoutShippingRates(): void
    {
        $quote = $this->checkoutSession->getQuote();
        if (!$quote || !$quote->getId() || $quote->isVirtual()) {
            return;
        }

        $shippingAddress = $quote->getShippingAddress();
        $countryId = (string) $shippingAddress->getCountryId();

        if ($countryId === '') {
            $countryId = $this->defaultCountryProvider->getDefaultCountry();
        }
        if ($countryId === '') {
            return;
        }

        $this->updateShippingRatesFromCountry($countryId, 'checkout');
    }

    /**
     * Product context: use store default country from config.
     */
    private function refreshProductShippingRates(): void
    {
        $countryId = $this->defaultCountryProvider->getDefaultCountry();
        if ($countryId === '') {
            return;
        }

        $quote = $this->checkoutSession->getQuote();
        $hasPersistedQuoteWithItems = ($quote && $quote->getId() && $quote->getItemsCount() > 0);

        if ($hasPersistedQuoteWithItems) {
            $this->updateShippingRatesFromCountry($countryId, 'product');
            return;
        }

        $previewRates = $this->estimatePreviewRatesForCurrentProduct($countryId, $this->productId, 1);
        if ($previewRates !== []) {
            $this->eceParams['resolvePayload']['shippingRates'] = $previewRates;
            $this->resolvePayload['shippingRates'] = $previewRates;
            //lets assume that first method will be always added to amount and callback
            //@TODO this needs to be evaluated so it would not add a double amount when amount is already up to date
            $this->eceParams['elementOptions']['amount'] = $this->eceParams['elementOptions']['amount'] + current($previewRates)['amount'];
        }
    }

    private function estimatePreviewRatesForCurrentProduct(string $countryId, ?int $productId, int $qty = 1): array
    {
        if (!$productId) {
            return [];
        }

        $store = $this->storeManager->getStore();
        $collection = $this->productCollectionFactory->create()
            ->setStoreId((int)$store->getId())
            ->addAttributeToSelect(['price', 'weight'])
            ->addAttributeToFilter('entity_id', $productId)
            ->setPageSize(1);

        $product = $collection->getFirstItem();

        $price = (float)($product->getFinalPrice($qty) ?: $product->getPrice() ?: 0.0);
        $weight = (float)$product->getWeight() ?: 0.0;

        $request = $this->rateRequestFactory->create();

        $store = $this->storeManager->getStore();
        $request->setDestCountryId($countryId);
        $request->setPackageValue($price * $qty);
        $request->setPackageValueWithDiscount($price * $qty);
        $request->setPackageWeight($weight * $qty);
        $request->setPackageQty($qty);
        $request->setStoreId((int)$store->getId());
        $request->setWebsiteId((int)$store->getWebsiteId());
        $request->setBaseSubtotalInclTax($price * $qty);
        $request->setFreeMethodWeight(0);

        $result = $this->shipping->collectRates($request)->getResult();
        if (!$result) {
            return [];
        }

        $rates = [];
        foreach ($result->getAllRates() as $rate) {
            $id = $rate->getCarrier() . '_' . $rate->getMethod();
            $label = $rate->getMethodTitle() ?: $rate->getCarrierTitle() ?: $id;
            $amountCents = (int)round(((float)$rate->getPrice()) * 100);

            $rates[] = [
                'id' => $id,
                'displayName' => $label,
                'amount' => $amountCents,
            ];
        }
        return $rates;
    }

    /**
     * Shared logic: calls the service and updates local state.
     */
    private function updateShippingRatesFromCountry(string $countryId, string $location): void
    {
        $address = ['country' => $countryId];

        if ($location === 'checkout') {
            $quote = $this->checkoutSession->getQuote();
            if ($quote && $quote->getId() && !$quote->isVirtual()) {
                $shipping = $quote->getShippingAddress();

                $city = (string)($shipping->getCity() ?? '');
                if ($city !== '') {
                    $address['city'] = $city;
                }

                $postcode = (string)($shipping->getPostcode() ?? '');
                if ($postcode !== '') {
                    $address['postal_code'] = $postcode;
                }

                $region = (string)($shipping->getRegion() ?: $shipping->getRegionCode() ?: '');
                if ($region !== '') {
                    $address['state'] = $region;
                }
            }
        }

        $json = $this->service->ece_shipping_address_changed($address, $location);
        $data = $this->serializer->unserialize($json);

        if (!empty($data['resolvePayload']['shippingRates'])) {
            $this->eceParams['resolvePayload']['shippingRates'] = $data['resolvePayload']['shippingRates'];
            $this->resolvePayload['shippingRates'] = $data['resolvePayload']['shippingRates'];
        }

        if (!empty($data['elementOptions']['amount'])) {
            $this->totalAmount = (int) $data['elementOptions']['amount'];
        }
    }

    private function fixEceParamsOverflow(): void
    {
        //this will show all buttons always
        $this->eceParams['layout'] = [
            'overflow' => 'never'
        ];
    }

    private function fixEceParamsCountries(): void
    {
        $this->eceParams['resolvePayload']['allowedShippingCountries'] =
            array_values($this->eceParams['resolvePayload']['allowedShippingCountries']);
    }

    private function setResolvePayload(array $payload): void
    {

        if (!empty($payload['shippingRates']) && is_array($payload['shippingRates']))
        {
            $payload['shippingAddressRequired'] = true;
        }

        $this->resolvePayload = $payload;

        $this->totalAmount = array_sum(array_column($payload['lineItems'], 'amount'));
    }
}
