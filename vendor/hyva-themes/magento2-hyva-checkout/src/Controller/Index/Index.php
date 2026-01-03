<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Controller\Index;

use Exception;
use Hyva\Checkout\Exception\NavigatorException;
use Hyva\Checkout\Exception\StepNotFoundException;
use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Checkout as SystemCheckoutConfig;
use Hyva\Checkout\Model\Layout as HyvaCheckoutLayout;
use Hyva\Checkout\Model\Navigation\Navigator;
use Hyva\Checkout\Model\Navigation\NavigatorInstructions;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page;
use Magento\Checkout\Controller\Action;
use Magento\Checkout\Exception as CheckoutException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Api\CartRepositoryInterface as QuoteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Index extends Action implements HttpGetActionInterface
{
    protected SessionCheckoutConfig $sessionCheckoutConfig;
    protected SessionCheckout $sessionCheckout;
    protected SessionCustomer $sessionCustomer;
    protected HyvaCheckoutConfig $hyvaCheckoutConfig;
    protected PageFactory $pageFactory;
    protected QuoteRepositoryInterface $quoteRepository;
    protected HyvaCheckoutLayout $layoutHyvaCheckout;
    protected LoggerInterface $logger;
    protected SystemCheckoutConfig $systemCheckoutConfig;
    protected StoreManagerInterface $storeManager;
    protected Navigator $navigator;

    public function __construct(
        Context $context,
        SessionCheckout $sessionCheckout,
        SessionCustomer $sessionCustomer,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        SessionCheckoutConfig $sessionCheckoutConfig,
        HyvaCheckoutConfig $hyvaCheckoutConfig,
        PageFactory $pageFactory,
        QuoteRepositoryInterface $quoteRepository,
        HyvaCheckoutLayout $layoutHyvaCheckout,
        LoggerInterface $logger,
        SystemCheckoutConfig $systemCheckoutConfig,
        StoreManagerInterface $storeManager,
        ?Navigator $navigator = null
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;
        $this->sessionCustomer = $sessionCustomer;
        $this->hyvaCheckoutConfig = $hyvaCheckoutConfig;
        $this->pageFactory = $pageFactory;
        $this->quoteRepository = $quoteRepository;
        $this->layoutHyvaCheckout = $layoutHyvaCheckout;
        $this->logger = $logger;
        $this->systemCheckoutConfig = $systemCheckoutConfig;
        $this->storeManager = $storeManager;

        $this->navigator = $navigator
            ?: ObjectManager::getInstance()->get(Navigator::class);

        parent::__construct(
            $context,
            $sessionCustomer,
            $customerRepository,
            $accountManagement
        );
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            if (! $quote->hasItems() || $quote->getHasError() || ! $quote->validateMinimumAmount()) {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }

            // generate session ID only if connection is unsecure according to issues in session_regenerate_id function.
            // @see http://php.net/manual/en/function.session-regenerate-id.php
            if (!$this->isSecureRequest()) {
                $this->_customerSession->regenerateId();
            }

            $page = $this->pageFactory->create();
            $step = $this->getRequest()->getParam('step');

            /** @var NavigatorInstructions $navigatorInstructions */
            $navigatorInstructions = $this->navigator->createInstructions();

            if ($step) {
                $navigatorInstructions->setStep($step);
            }

            $this->initCheckout($page, $navigatorInstructions);

            $page->getConfig()->getTitle()->set(__('Checkout'));
            return $page;
        } catch (StepNotFoundException $exception) {
            $this->messageManager->addErrorMessage(__('Step not found.'));
            $this->logger->notice($exception->getMessage(), ['exception' => $exception]);

            return $this->resultRedirectFactory->create()->setPath('checkout');
        } catch (CheckoutException | Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong while trying to load the checkout.'));
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
    }

    /**
     * @throws CheckoutException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function initCheckout(Page $page, ?NavigatorInstructions $navigatorInstructions = null): Index
    {
        try {
            $this->navigator->start($navigatorInstructions);

            if ($this->navigator->getMemory()->isFirstAttempt()) {
                $this->tryToAssignCustomer();

                $this->_eventManager->dispatch('hyva_checkout_init_after', [
                    'session_checkout_config' => $this->sessionCheckoutConfig
                ]);
            }

            // With an active checkout, apply the corresponding step layout update handles to the Page object.
            $this->navigator->getActiveStep()->getLayout()->applyUpdateHandlesToPage($page);
        } catch (NavigatorException $exception) {
            $customer = $this->_customerSession->getCustomerDataObject();
            $id = $customer->getId();

            if ($id) {
                throw new CheckoutException(__('No checkout steps found (customer ID: %1)', $id));
            }

            throw new CheckoutException(__('No checkout steps found'));
        }

        return $this;
    }

    /**
     * @deprecated this method is deprecated since version 1.1.18. Use Navigator class instead.
     * @see Navigator
     */
    public function requiresReinitialization(): bool
    {
        return $this->navigator->getMemory()->isFirstAttempt();
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function tryToAssignCustomer(): void
    {
        /*
         * Want to load the correct customer information by assigning to address
         * instead of just loading from sales/quote_address.
         */
        $customer = $this->sessionCustomer->getCustomerDataObject();

        $this->sessionCheckout->getQuote()->assignCustomer($customer);
    }

    /**
     * Checks if the current request uses SSL and the referer also is secure.
     *
     * @return bool
     */
    private function isSecureRequest(): bool
    {
        $request = $this->getRequest();

        $referrer = $request->getHeader('referer');
        $secure = false;

        if ($referrer) {
            //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            $scheme = parse_url($referrer, PHP_URL_SCHEME);
            $secure = $scheme === 'https';
        }

        return $secure && $request->isSecure();
    }
}
