<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model;

use Exception;
use Hyva\Checkout\Exception\CheckoutNotFoundException;
use Hyva\Checkout\Exception\CheckoutException;
use Hyva\Checkout\Model\Config as HyvaCheckoutConfig;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Session\SessionStartChecker;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @method string|null getCurrent()
 * @method Session setCurrent(string $stepName)
 * @method Session unsCurrent()
 * @method string|null getPrevious()
 * @method Session setPrevious(string $stepName)
 * @method Session unsPrevious()
 * @method array|null getStepData()
 * @method Session setStepData(array $steps)
 * @method Session unsStepData()
 * @method Session setHash(string $hash)
 * @method string getHash()
 * @method Session unsHash()
 * @method Session setCheckoutNamespace(string $namespace)
 * @method string|null getCheckoutNamespace()
 * @method Session unsCheckoutNamespace()
 * @method string getStoreCode()
 * @method Session unsStoreCode()
 * @method Session setStoreCode(string $id)
 *
 * @deprecated stepping management has been taken over by the Navigator.
 *             the current state of the checkout is no longer stored in the session.
 *             instead, it is now handled by the page route itself.
 *
 * @see Navigator
 */
class Session extends SessionManager
{
    protected CustomConditionFactory $customConditionFactory;
    protected CustomConditionProcessor $customConditionProcessor;
    protected SerializerInterface $serializer;
    protected StoreManagerInterface $storeManager;

    /**
     * @throws SessionException
     */
    public function __construct(
        Http $request,
        SidResolverInterface $sidResolver,
        ConfigInterface $sessionConfig,
        SaveHandlerInterface $saveHandler,
        ValidatorInterface $validator,
        StorageInterface $storage,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        State $appState,
        CustomConditionFactory $customConditionFactory,
        CustomConditionProcessor $customConditionProcessor,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        ?SessionStartChecker $sessionStartChecker = null
    ) {
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $sessionStartChecker
        );

        $this->customConditionFactory = $customConditionFactory;
        $this->customConditionProcessor = $customConditionProcessor;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
    }

    /**
     * @throws CheckoutNotFoundException
     * @throws NoSuchEntityException
     */
    public function initStepData(HyvaCheckoutConfig $config): Session
    {
        $this->reset();
        $checkout = $config->getActiveCheckoutData();

        array_filter($checkout['steps'] ?? [], function ($step) {
            if (is_array($step['conditions'])) {
                $instances = $this->customConditionFactory->produce(
                    array_column($step['conditions'], 'type')
                );

                foreach ($step['conditions'] as $condition) {
                    try {
                        $instance = $instances[$condition['type']];
                        $method = $condition['method'];

                        if ($this->customConditionProcessor->isNotApplicable($instance, $method)) {
                            return false;
                        }
                    } catch (Exception $exception) {
                        return false;
                    }
                }
            }

            $this->setStepConfig($step['name'], $step);
            return true;
        });

        $this->setHash($checkout['hash'] ?? null);
        $this->setCheckoutNamespace($config->getActiveCheckoutNamespace());
        $this->setStoreCode($this->storeManager->getStore()->getCode());

        return $this;
    }
    /**
     * Associate data to a specified step of the checkout process.
     */
    public function setStepConfig($step, $data, $value = null): Session
    {
        $steps = $this->getStepData();

        // Overwrite an entire step.
        if ($value === null && is_array($data)) {
            $steps[$step] = $data;
        }
        // Unset step data by key.
        if ($value === null && is_string($data)) {
            unset($steps[$step][$data]);
        }
        // Set or overwrite step data by a key.
        if ($value !== null && is_string($data)) {
            $steps[$step][$data] = $value;
        }

        $this->setStepData($steps);
        return $this;
    }

    /**
     * Return the data associated to a specified step.
     */
    public function getStepConfig($step = null, $data = null)
    {
        $steps = $this->getStepData();

        if ($step === null) {
            return false;
        }
        if (!isset($steps[$step])) {
            return false;
        }
        if ($data === null) {
            return $steps[$step];
        }
        if (!is_string($data) || !isset($steps[$step][$data])) {
            return false;
        }

        return $steps[$step][$data];
    }

    /**
     * Get all steps.
     */
    public function getSteps(): ?array
    {
        return $this->getStepData();
    }

    public function hasSteps(): bool
    {
        $steps = $this->getSteps();
        return is_array($steps) && count($steps) !== 0;
    }

    /**
     * Get a specific step by its name.
     */
    public function getStep(
        string $name,
        ?string $key = null,
        int $position = 0,
        string $operator = '+',
        int $length = 1
    ) {
        $steps = $this->getSteps();
        $current = $steps ? array_search($name, array_keys($steps), true) : false;

        if ($current === false) {
            return null;
        }

        $offset = $operator === '+' ? $current + $position : $current - $position;
        $search = current(array_slice($steps, $offset, $length));

        if ($length === 1 && is_string($key)) {
            return $search[$key] ?? null;
        }

        return $search;
    }

    /**
     * Get a specific step by its route.
     */
    public function getStepByRoute(string $route)
    {
        $steps  = $this->getSteps();
        $search = array_keys(array_combine(array_keys($steps), array_column($steps, 'route')), $route);

        return count($search) > 0 ? $steps[$search[0]] : false;
    }

    /**
     * Validate if step exists.
     */
    public function stepExists(string $name): bool
    {
        return (bool) $this->getStep($name);
    }

    /**
     * Save a step as the current step and define the current as previous.
     *
     * @throws CheckoutException
     */
    public function setCurrentStep(string $name): Session
    {
        $search = $this->getStep($name);

        if (! $search) {
            $search = $this->getFirstStep();

            if (! $search) {
                throw new CheckoutException(__('Step %1 cannot be set as the current step', $name));
            }
        }

        return $this->setCurrent($search['name']);
    }

    public function getCurrentStep(?string $key = null)
    {
        $current = $this->getCurrent();

        if (is_string($current)) {
            return $this->getStep($this->getCurrent(), $key);
        }

        return null;
    }

    public function getPreviousStep(?string $key = null)
    {
        $previous = $this->getPrevious();

        if (is_string($previous)) {
            return $this->getStep($this->getPrevious(), $key);
        }

        return null;
    }

    public function getFirstStep(?string $key = null)
    {
        $steps = $this->getSteps();
        $search = count($steps) === 0 ? null : current($steps);

        return $search ? $this->getStep($search['name'], $key) : false;
    }

    public function getLastStep(?string $key = null)
    {
        $steps = $this->getSteps();
        $search = count($steps) === 0 ? null : end($steps);

        return $this->getStep($search['name'], $key);
    }

    public function getStepAfter(?string $for = null): ?array
    {
        $target = $for ? $this->getStep($for) : $this->getCurrentStep();

        if ($target === null) {
            return null;
        }

        $next = $this->getStep($target['name'], null, 1);
        return is_array($next) && $next['position'] > $target['position'] ? $next : null;
    }

    public function getStepBefore(?string $for = null): ?array
    {
        $target = $for ? $this->getStep($for) : $this->getCurrentStep();

        if ($target === null) {
            return null;
        }

        $previous = $this->getStep($target['name'], null, 1, '-');
        return is_array($previous) && $previous['position'] < $target['position'] ? $previous : null;
    }

    /**
     * Check if we can move towards a target based on the current position (step).
     */
    public function canStepTo(string $name, bool $validate = false): bool
    {
        $steps = $this->getSteps();
        $current = $this->getCurrentStep();

        if ($current === false || isset($steps[$name]) === false || $this->isSingleStepper()) {
            return false;
        }

        $target = $steps[$name];

        if ($target['position'] <= $current['position']) {
            return true;
        }
        if ($validate === false && $target['position'] + 1 > $current['position']) {
            return false;
        }

        $stepKeys = array_keys($steps);
        $positionCurrent = array_search($current['name'], $stepKeys, true);
        $positionTarget = array_search($target['name'], $stepKeys, true);
        $stepPieces = array_slice($steps, $positionCurrent, $positionTarget);
        $previous = true;

        $inspect = array_filter($stepPieces, static function (array $step) use ($target, $validate, &$previous) {
            /*
             * Validation is required but also a move into an unvalidated step. So it needs to
             * allow a 'step into' when the current step is the same as the targeted step.
             */
            if ($validate && $step['position'] === $target['position']) {
                return true;
            }
            if ($previous && isset($step['validation'])) {
                $filter = array_filter($step['validation'], static function ($validation) {
                    return $validation['result'] === true;
                });

                $previous = count($step['validation']) === count($filter);
            }

            return $previous;
        });

        return $validate ? count($stepPieces) === count($inspect) : isset($inspect[$target['name']]);
    }

    /**
     * Set the current step to the first available.
     *
     * @throws CheckoutException
     */
    public function restart(): Session
    {
        $startingStepName = $this->getFirstStep('name');

        if ($startingStepName) {
            $this->setCurrentStep($startingStepName);
        }

        return $this->reset('previous');
    }

    /**
     * Reset session data.
     */
    public function reset(?string $key = null, $value = null): Session
    {
        if ($key !== null) {
            return $this->setData($key, $value);
        }

        return $this->unsStepData()->unsCurrent()->unsHash();
    }

    public function isSingleStepper(): bool
    {
        return count($this->getSteps()) === 1;
    }

    public function isMultiStepper(): bool
    {
        return count($this->getSteps()) > 1;
    }
}
