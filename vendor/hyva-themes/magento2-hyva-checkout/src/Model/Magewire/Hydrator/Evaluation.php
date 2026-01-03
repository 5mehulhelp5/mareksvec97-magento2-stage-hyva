<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Magewire\Hydrator;

use Exception;
use Hyva\Checkout\Magewire\Main;
use Hyva\Checkout\Model\ConfigData\HyvaThemes\Developer\SystemConfigExperimental;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultManagement;
use Hyva\Checkout\Model\Session as SessionCheckoutConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as ApplicationState;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magewirephp\Magewire\Component;
use Magewirephp\Magewire\Model\HydratorInterface;
use Magewirephp\Magewire\Model\LayoutRenderLifecycle;
use Magewirephp\Magewire\Model\RequestInterface;
use Magewirephp\Magewire\Model\ResponseInterface;
use Psr\Log\LoggerInterface;

class Evaluation implements HydratorInterface
{
    private array $evaluations = [];

    public function __construct(
        protected SessionCheckoutConfig $sessionCheckoutConfig,
        protected EventManagerInterface $eventManager,
        protected EvaluationResultFactory $evaluationResultFactory,
        protected LayoutRenderLifecycle $layoutRenderLifecycle,
        protected SerializerInterface $serializer,
        protected SystemConfigExperimental|null $systemConfigExperimental = null,
        private LoggerInterface|null $logger = null,
        private ApplicationState|null $applicationState = null,
        private EvaluationResultManagement|null $evaluationResultManagement = null
    ) {
        $this->systemConfigExperimental ??= ObjectManager::getInstance()->get(SystemConfigExperimental::class);

        $this->logger ??= ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->applicationState ??= ObjectManager::getInstance()->get(ApplicationState::class);
        $this->evaluationResultManagement ??= ObjectManager::getInstance()->get(EvaluationResultManagement::class);
    }

    // phpcs:ignore
    public function hydrate(Component $component, RequestInterface $request): void
    {
    }

    public function dehydrate(Component $component, ResponseInterface $response): void
    {
        if ($this->canHydrate($component) === false) {
            return;
        }

        $evaluationResult = $this->isEvaluationComponent($component)
            ? $this->evaluateComponent($component)
            : $this->compileEvaluationResult($component, $this->evaluationResultManagement->processModificationsForComponent($component));

        // This block is part of an experimental feature, currently enabled by default.
        if ($this->systemConfigExperimental->disableMainEvaluationResultMerge()) {
            $response->memo['evaluation'] = [$component->id => $evaluationResult];
            return;
        }

        // In future versions of the checkout, the below code will be deprecated and subject to removal.
        $this->evaluations[$component->id] = $evaluationResult;
        $response->memo['evaluation'] = $component::COMPONENT_TYPE === Main::COMPONENT_TYPE
            ? $this->evaluations
            : [$component->id => $evaluationResult];
    }

    public function isEvaluationComponent(Component $component): bool
    {
        return $component instanceof EvaluationInterface;
    }

    /**
     * @param Component & EvaluationInterface $component
     */
    public function evaluateComponent(Component $component): array
    {
        try {
            $evaluationCompletionResult = $component->evaluateCompletion($this->evaluationResultFactory);
        } catch (Exception $exception) {
            $evaluationCompletionResult = $this->evaluationResultFactory->createBatch();

            // Inform the customer that a technical evaluation malfunction happened.
            $evaluationCompletionResult->push(
                $this->evaluationResultFactory
                    ->createMessageDialog('Evaluation Malfunction')
                    ->presetAsTechnicalMalfunction()
            );

            // Pushes an exception message as a console error log when in developer mode.
            $evaluationCompletionResult->push(
                $this->evaluationResultFactory
                    ->createConsoleLog($exception->getMessage())
                    ->withMessageTitle('Evaluation API')
                    ->withAlias('evaluation-exception-log')
                    ->asError()
            );

            $this->logger->critical(
                sprintf('Something went wrong during a component evaluation: "%s".', get_class($component)),
                [
                    'exception' => $exception
                ]
            );
        }

        $compilation = $this->evaluationResultManagement->processModificationsForComponent($component, $evaluationCompletionResult);
        return $this->compileEvaluationResult($component, $compilation);
    }

    public function compileEvaluationResult(Component $component, EvaluationResultInterface $result): array
    {
        $data = [
            'arguments' => $result->getArguments($component),
            'dispatch' => false,
            'result' => $result->getResult(),
            'group' => $result->getType() . '-' . $component->id,
            'type' => $result->getType(),
            'id' => $component->id,

            // @deprecated blocking is defined by a blocking capability trait and should sit in the arguments array.
            'blocking' => $result->isBlocking()
        ];

        if ($result instanceof EvaluationResult) {
            $data['name'] = $result->getName();

            if (method_exists($result, 'canDispatch')) {
                $data['dispatch'] = $result->canDispatch();
            }
        }

        $data['hash'] = sha1(json_encode($data));
        return $data;
    }

    /**
     * Validate if the current component needs to be de- or hydrated.
     */
    public function canHydrate(Component $component): bool
    {
        $current = $this->sessionCheckoutConfig->getCurrentStep();

        if ($current === false) {
            return false;
        }

        $previous = $this->sessionCheckoutConfig->getPreviousStep();

        if (($previous === null && $current !== null)
            || (is_array($current)
                && is_array($previous)
                && $current['position'] !== $previous['position'])) {
            return true;
        }

        $parent = $component->getParent();
        return $parent && $this->layoutRenderLifecycle->isChild($parent->getNameInLayout());
    }
}
