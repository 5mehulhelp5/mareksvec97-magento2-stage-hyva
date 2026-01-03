<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Magewire;

use Hyva\Checkout\Exception\NavigatorStepNotFoundException;
use Hyva\Checkout\Model\Checkout\Step;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigPlaceOrder;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Batch as EvaluationResultBatch;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\PlaceOrderRedirect;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\Redirect;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\MainInterface;
use Hyva\Checkout\Model\Magewire\Payment\PlaceOrderServiceProcessor;
use Hyva\Checkout\Model\Magewire\Payment\PlaceOrderServiceProvider;
use Hyva\Checkout\Model\Magewire\ServerMemoConfig\AbstractConfigSection;
use Hyva\Checkout\Model\Navigation\Navigator;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Magewirephp\Magewire\Component;
use Psr\Log\LoggerInterface;

class Main extends Component implements MainInterface, EvaluationInterface
{
    public const COMPONENT_TYPE = 'hyva-checkout-main';

    // Frontend API configuration.
    public array $config = [];

    protected $loader = [
        MainInterface::METHOD_NAVIGATE => true,
        MainInterface::METHOD_PLACE_ORDER => 'Processing your order'
    ];

    protected SessionCheckoutConfig $sessionCheckoutConfig;
    protected SessionCheckout $sessionCheckout;
    protected EventManagerInterface $eventManager;
    protected PlaceOrderServiceProcessor $placeOrderServiceProcessor;
    protected PlaceOrderServiceProvider $placeOrderServiceProvider;
    protected LoggerInterface $logger;
    /** @var AbstractConfigSection[] $serverMemoDataConfig */
    protected array $serverMemoDataConfig;

    private ?EvaluationResultBatch $evaluationResultBatch;
    private ?Navigator $navigator;
    private ?SystemConfigPlaceOrder $systemConfigPlaceOrder;

    public function __construct(
        SessionCheckoutConfig $sessionCheckoutConfig,
        SessionCheckout $sessionCheckout,
        EventManagerInterface $eventManager,
        PlaceOrderServiceProcessor $placeOrderServiceProcessor,
        PlaceOrderServiceProvider $placeOrderServiceProvider,
        LoggerInterface $logger,
        ?EvaluationResultBatch $evaluationResultBatch = null,
        ?Navigator $navigator = null,
        ?SystemConfigPlaceOrder $systemConfigPlaceOrder = null,
        array $serverMemoDataConfig = []
    ) {
        $this->sessionCheckoutConfig = $sessionCheckoutConfig;
        $this->sessionCheckout = $sessionCheckout;
        $this->eventManager = $eventManager;
        $this->placeOrderServiceProcessor = $placeOrderServiceProcessor;
        $this->placeOrderServiceProvider = $placeOrderServiceProvider;
        $this->logger = $logger;
        $this->serverMemoDataConfig = $serverMemoDataConfig;

        $this->evaluationResultBatch = $evaluationResultBatch
            ?: ObjectManager::getInstance()->get(EvaluationResultBatch::class);
        $this->navigator = $navigator
            ?: ObjectManager::getInstance()->get(Navigator::class);
        $this->systemConfigPlaceOrder = $systemConfigPlaceOrder
            ?: ObjectManager::getInstance()->get(SystemConfigPlaceOrder::class);
    }

    public function mount(): void
    {
        $this->processConfig();
    }

    /**
     * The $route argument value is at this point not the source of truth but rather the required
     * step to move into independently of the validation. The validation upfront in the lifecycle.
     * Therefore, we can not use it because the validation could have failed along the way.
     */
    public function navigateToStep(string $route): void
    {
        try {
            $step = $this->navigator->stepToByRoute($route);
            $this->processConfig();

            if ($step->getRoute() === $route) {
                $this->getEvaluationResultBatch()->push(
                    $this->evaluationResultBatch->getFactory()->createEvent('checkout:navigation:success')
                        ->withDetails($step->toPublicDataArray())
                        ->dispatch()
                );
            }
        } catch (LocalizedException|NavigatorStepNotFoundException $exception) {
            $this->dispatchErrorMessage(
                'Whoops, our bad... the step you requested was not found.'
            );

            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
    }

    public function placeOrder(array $data = []): void
    {
        if ($this->validateCascadingStepData($data)) {
            $this->placeOrderServiceProcessor->process($this, null, $data);
        }
        if ($this->placeOrderServiceProcessor->hasSuccess()) {
            $this->navigator->finish();
        }

        /*
         * Since there is no next page after a successful order placement, this process depends on the frontend
         * implementation for supporting direct dispatcher. Evaluation results that can be dispatched are executed
         * promptly upon a successful order, providing a seamless user experience.
         */
        if ($this->getEvaluationResultBatch()->containsSuccessResults(true)) {
            $this->navigator->finish();

            /*
             * Since the quote is no longer available, the main component should abstain from rendering any subcomponents.
             * Rendering subcomponents may result in exceptions due to data dependencies on the absent quote.
             * Consequently, the main component necessitates a redirect template to inform the customer appropriately.
             */
            if ($this->getEvaluationResultBatch()->owns(fn ($value) => $value instanceof Redirect)) {
                // Backward compatibility: set the default redirect template if none was provided.
                $this->switchTemplate('Hyva_Checkout::main/place-order/redirect.phtml');

                $this->getEvaluationResultBatch()->walk(function (EvaluationResult $result) {
                    if ($result instanceof Redirect) {
                        $result->withTimeout($this->systemConfigPlaceOrder->getSuccessPageRedirectTimeout());

                        if ($result instanceof PlaceOrderRedirect) {
                            $this->switchTemplate($result->getRedirectTemplate());
                        }
                    }
                });
            }
        }

        // Dispatch each batch item individually to ensure direct handling on the frontend by its counterpart.
        $this->getEvaluationResultBatch()->dispatch();
        // Re-process server memo configuration items.
        $this->processConfig();
    }

    public function booted(): void
    {
        $step = $this->navigator->getActiveStep();

        /** @see \Hyva\Checkout\Model\Config\Converter::includeObservableEvents() */
        foreach ($step->getEvents() ?? [] as $event) {
            $this->eventManager->dispatch(sprintf($event, 'booted'), [
                'session_checkout_config' => $this->sessionCheckoutConfig
            ]);
        }
    }

    public function dehydrate(): void
    {
        $checkout = $this->navigator->getActiveCheckout();
        $step = $this->navigator->getActiveStep();

        /** @see \Hyva\Checkout\Model\Config\Converter::includeObservableEvents() */
        foreach ($step->getEvents() ?? [] as $event) {
            if (! $checkout->getStepBefore($step)) {
                $this->eventManager->dispatch(sprintf($event, 'init'), [
                    'session_checkout_config' => $this->sessionCheckoutConfig
                ]);
            }
        }
    }

    public function getEvaluationResultBatch(): EvaluationResultBatch
    {
        return $this->evaluationResultBatch;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResult
    {
        $batch = $this->getEvaluationResultBatch();

        if ($batch->isEmpty()) {
            return $resultFactory->createSuccess();
        }

        return $batch;
    }

    protected function processConfig(): void
    {
        foreach ($this->serverMemoDataConfig as $namespace => $config) {
            if (array_key_exists($namespace, $this->config) && $config->isStaticData()) {
                continue;
            }

            $sectionData = $config->getData();
            $sectionDataInjection = $config->getDataInjection();

            if ($sectionDataInjection) {
                $sectionData = array_merge_recursive($sectionData, $sectionDataInjection);
            }

            $this->config[$namespace] = $sectionData;
        }
    }

    /**
     * This single validation method will eventually end up in a composite validator that can validate multiple
     * validators where if one fails, it needs to push a result into the Evaluation Result Batch and return false.
     *
     * As long as all return true, the first barrier to successfully place the order via the Place Order Service has been taken.
     */
    private function validateCascadingStepData(array $data): bool
    {
        $cascadingStepData = $data['cascading-step-data'] ?? false;

        // Grep all available step for the current customer.
        $expected = array_map(fn (Step $step) => $step->getRoute(), $this->navigator->getActiveCheckout()->getAvailableSteps());
        // Bucket for any missing steps.
        $missing  = [];
        // Bucket for any failed steps.
        $failing  = [];

        if (is_array($cascadingStepData)) {
            foreach ($expected as $route) {
                if (! isset($cascadingStepData[$route])) {
                    $missing[] = $route;
                } elseif ($cascadingStepData[$route] !== true) {
                    $failing[] = $route;
                }
            }

            $evaluationResultsFactory = $this->getEvaluationResultBatch()->getFactory();
            $messageDialog = $evaluationResultsFactory->createMessageDialog('Incomplete Checkout Process');

            if (count($missing) !== 0) {
                $messageDialog->withMessage('It looks like some steps were missed along the way. Don’t worry, we will ' .
                    'take you back to the first step that needs to be completed. Please review it, then continue with your checkout.')
                    ->withConfirmationCallback($evaluationResultsFactory
                            ->createExecutable('checkout.navigation.step-to')
                        ->withParam('route', $missing[0]))
                    ->withTag('missing');
            } elseif (count($failing) !== 0) {
                $messageDialog->withMessage('Oops! It looks like some steps were not fully completed. We’ll guide you ' .
                    'back to the first step that needs your attention. Please review and complete it before proceeding.')
                    ->withConfirmationCallback($evaluationResultsFactory
                        ->createExecutable('checkout.navigation.step-to')
                        ->withParam('route', $failing[0]))
                    ->withTag('failing');
            }

            if ($messageDialog->containsTags(['missing', 'failing'])) {
                $this->getEvaluationResultBatch()->push($messageDialog);

                return false;
            }
        }

        return true;
    }
}
