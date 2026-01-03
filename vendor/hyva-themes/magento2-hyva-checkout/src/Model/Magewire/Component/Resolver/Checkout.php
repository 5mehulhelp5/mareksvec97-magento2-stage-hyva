<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Component\Resolver;

use Hyva\Checkout\Exception\CheckoutException;
use Hyva\Checkout\Model\Layout as CheckoutLayout;
use Hyva\Checkout\Model\Magewire\UpdateAdapterInterface;
use Hyva\Checkout\Model\Navigation\Navigator;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;
use Magewirephp\Magewire\Component;
use Magewirephp\Magewire\Exception\MissingComponentException;
use Magewirephp\Magewire\Model\Component\Resolver\Layout as LayoutResolver;
use Magewirephp\Magewire\Model\ComponentFactory;
use Magewirephp\Magewire\Model\RequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Checkout extends LayoutResolver
{
    protected UpdateAdapterInterface $updateAdapter;
    protected CheckoutLayout $layout;
    protected SessionCheckoutConfig $sessionCheckoutConfig;

    private Navigator $navigator;

    public function __construct(
        ResultPageFactory $resultPageFactory,
        EventManagerInterface $eventManager,
        ComponentFactory $componentFactory,
        UpdateAdapterInterface $updateAdapter,
        CheckoutLayout $layout,
        SessionCheckoutConfig $sessionCheckoutConfig,
        ?Navigator $navigator = null
    ) {
        parent::__construct($resultPageFactory, $eventManager, $componentFactory);

        $this->updateAdapter = $updateAdapter;
        $this->layout = $layout;
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;

        $this->navigator = $navigator
            ?: ObjectManager::getInstance()->get(Navigator::class);
    }

    public function getName(): string
    {
        return 'hyva_checkout';
    }

    public function complies(AbstractBlock $block): bool
    {
        // When the navigator is active, we assume the user is on the checkout page.
        // Therefore, each component should be treated as part of the checkout process.
        return $this->navigator->isRunning();
    }

    /**
     * @throws MissingComponentException
     * @throws CheckoutException|LocalizedException
     */
    public function reconstruct(RequestInterface $request): Component
    {
        $navigation = $request->getServerMemo('navigation');

        if ($this->navigator->isNotRunning()) {
            $this->navigator->start(
                $this->navigator->createInstructions()
                    ->setCheckout($navigation['checkout'])
                    ->setStep($navigation['step'])
            );
        }

        $page = $this->resultPageFactory->create();
        $page->addHandle(strtolower($request->getFingerprint('handle')))->initLayout();

        if ($this->updateAdapter->belongsToNavigationComponent($request)) {
            $this->processNavigationRequest($page, $request);
        } else {
            $this->processComponentRequest($page, $request);
        }

        /** @var Template|false $block */
        $block = $page->getLayout()->getBlock($request->getFingerprint('name'));

        if ($block === false) {
            throw new HttpException(404, 'Component could not be found.');
        }

        return $this->construct($block);
    }

    public function processNavigationRequest(Page $page, RequestInterface $request): void
    {
        $current = $this->navigator->getActiveStep();

        foreach ($request->getUpdates() as $update) {
            if ($this->updateAdapter->isNavigationUpdateRequest($update) === false) {
                $this->layout->applyUpdateHandles($page, $current->getUpdates());
                continue;
            }

            // Try to locate the required step.
            $target = $this->navigator->getActiveCheckout()->getStepByRoute(
                $this->updateAdapter->locateStep($update) ?? $current->getRoute()
            );

            // Apply optional step layout update handles.
            $this->layout->applyUpdateHandles($page, $target->getUpdates());
        }
    }

    public function processComponentRequest(Page $page, RequestInterface $request)
    {
        $updates = $this->navigator->getActiveStep()->getUpdates();

        if ($updates) {
            $this->layout->applyUpdateHandles($page, $updates);
        }
    }
}
