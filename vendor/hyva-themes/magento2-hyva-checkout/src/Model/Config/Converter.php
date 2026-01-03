<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use Hyva\Checkout\Model\Config\Converter\IncludeConfigUpdates;
use Hyva\Checkout\Model\Config\Converter\IncludeConfigUpdatesFactory;
use Hyva\Checkout\Model\CustomConditionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Model\ScopeInterface;

class Converter implements ConverterInterface
{
    /* Common attributes */
    public const ATTRIBUTE_NAME   = 'name';
    public const ATTRIBUTE_BEFORE = 'before';
    public const ATTRIBUTE_AFTER  = 'after';
    public const ATTRIBUTE_REMOVE = 'remove';
    public const ATTRIBUTE_IF     = 'if';

    public const LAYOUT_HANDLE_FALLBACK = '2columns';

    protected ScopeConfigInterface $scopeConfig;
    protected EncryptorInterface $encryptor;
    protected JsonSerializer $jsonSerializer;
    protected IncludeConfigUpdatesFactory $includeConfigUpdatesFactory;

    private array $checkouts = [];

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        JsonSerializer $jsonSerializer,
        IncludeConfigUpdatesFactory $includeConfigUpdatesFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->jsonSerializer = $jsonSerializer;
        $this->includeConfigUpdatesFactory = $includeConfigUpdatesFactory;
    }

    public function convert($source): array
    {
        $result = [];

        if (! $source instanceof DOMDocument) {
            return $result;
        }

        $result['checkouts'] = $this->includeCheckouts($source->getElementsByTagName('checkout'));
        return $result;
    }

    /**
     * <checkout...
     */
    public function includeCheckouts(DOMNodeList $checkouts): array
    {
        $result = [];

        /** @var DOMElement $checkout */
        foreach ($checkouts as $checkout) {
            $name    = $checkout->getAttribute(self::ATTRIBUTE_NAME);
            $label   = $checkout->getAttribute('label');
            $parent  = $checkout->getAttribute('parent');
            $visible = $checkout->getAttribute('visible');

            // Temporary store an unsorted list of DOMElement objects.
            $this->checkouts[$name] = $checkout;

            $result[$name] = [
                'name'    => $name,
                'label'   => empty($label) ? $name : ucfirst($label),
                'parent'  => empty($parent) || $parent === $name ? null : $parent,
                'visible' => empty($visible) || $visible === 'true'
            ];
        }

        return array_map(function (array $checkout): array {
            $target = $this->checkouts[$checkout['name']];
            // Start position of each step.
            $position = 0;

            $checkout['steps'] = array_map(function ($step) use ($target, &$position) {
                return $this->finalizeStep($step, $target, $position);
            }, $this->includeSteps($target) ?? []);

            $checkout['sequence'] = $this->includeCheckoutSequence($target);
            $checkout['hash'] = $this->encryptor->hash($this->jsonSerializer->serialize($checkout));

            return $checkout;
        }, $this->sortWithBeforeAfter($result ?? [], null, null, 'parent', false));
    }

    /**
     * <checkout>
     *     <step...
     */
    public function includeSteps(DOMElement $checkout, ?DOMElement $root = null): array
    {
        $root ??= $checkout;

        $steps = $this->lookupSteps($checkout);
        $parent = $this->lookupAncestor($checkout);
        $result = [];

        /** @var DOMElement $step */
        foreach ($steps as $step) {
            $stepName = $step->getAttribute(self::ATTRIBUTE_NAME);

            if (! empty($stepName)) {
                $result[$stepName] = $this->includeStep($step, $checkout, $parent, $root);
            }
        }

        return $this->sortWithBeforeAfter(
            $parent ? $this->merge($this->includeSteps($parent, $root), $result) : $result
        );
    }

    public function includeStep(DOMElement $step, DOMElement $checkout, ?DOMElement $parent = null, ?DOMElement $root = null)
    {
        $stepClone  = $step->getAttribute('clone');
        $stepName   = $step->getAttribute(self::ATTRIBUTE_NAME);
        $stepConfig = $step->getAttribute('ifconfig');
        $stepRemove = $step->getAttribute(self::ATTRIBUTE_REMOVE) === 'true';

        $result = [];

        if ($stepRemove || (! empty($stepConfig) && ! $this->ifconfig($stepConfig))) {
            if ($parent !== null) {
                $result = self::ATTRIBUTE_REMOVE;
            }

            return $result;
        }

        $stepLabel = $step->getAttribute('label');
        $stepRoute = $step->getAttribute('route');

        // Start to fill the step.
        $result['name']  = $stepName;
        $result['label'] = $stepLabel;
        $result['route'] = empty($stepRoute) ? $stepName : $stepRoute;

        $stepBefore = $step->getAttribute(self::ATTRIBUTE_BEFORE);
        $stepAfter  = $step->getAttribute(self::ATTRIBUTE_AFTER);

        // Fill optional before/after siblings.
        if (! empty($stepBefore)) {
            $result[self::ATTRIBUTE_BEFORE] = $stepBefore;
        } elseif (! empty($stepAfter)) {
            $result[self::ATTRIBUTE_AFTER] = $stepAfter;
        }

        if ($parent) {

            /*
             * The checkout has a parent, but is adding a new step which is not included in the parent.
             * Therefor the step still needs the global layout handles based on the parent(s) but without
             * the child specific ones because those simply don't exist. These have to be injected first
             * before the general step layout updates are being included.
             */
            $parentUpdateConfig = $this->includeConfigUpdatesFactory->create(['applyStepHandles' => false]);
            $result['updates'] = $this->includeUpdates($step, $parent, $root, $parentUpdateConfig);

        }

        // Include support for layout updates.
        $result['updates'] = $this->merge($result['updates'] ?? [], $this->includeUpdates($step, $checkout, $root));
        // Include support for custom visibility conditions.
        $result['conditions'] = $this->merge($result['conditions'] ?? [], $this->includeCustomConditions($step, $checkout));
        // Inject both checkout and step related observer events.
        $result['events'] = $this->includeObservableEvents($step, $checkout);

        // Include support for cloning steps from different checkouts.
        if (! empty($stepClone)) {
            $result = $this->lookupStepClone($stepClone, $result);
        }

        return $parent ? $this->merge($this->includeStep($step, $parent, null, $root), $result) : $result;
    }

    /**
     * <checkout>
     *     <step>
     *         <update...
     */
    public function includeUpdates(
        DOMElement $step,
        DOMElement $checkout,
        ?DOMElement $root = null,
        ?IncludeConfigUpdates $includeConfigUpdates = null
    ): ?array {
        if ($includeConfigUpdates === null) {
            $includeConfigUpdates = $this->includeConfigUpdatesFactory->create();
        }

        $checkoutName = $checkout->getAttribute(self::ATTRIBUTE_NAME);
        $stepName = $step->getAttribute(self::ATTRIBUTE_NAME);
        $stepLayoutUpdates = $step->getElementsByTagName('update');
        $stepLayout = $step->getAttribute('layout');

        $parent = $this->lookupAncestor($checkout);
        $result = [];

        /*
         * Apply the layout for this step.
         *
         * When the attribute isn't empty, we just set the handle name without the default
         * "hyva_checkout_layout" prefix. When empty, just return null in order to apply
         * the final handle at the end of the convert.
         */
        $result['layout']['handle'] = empty($stepLayout) ? null : $stepLayout;

        /*
         * Reason for having a unique key is for when parent and child checkouts get merged.
         * Without having a unique identifier, child handles will overwrite those of the parent.
         *
         * Besides from that, the parent is always prior to its child. The unique id will be a
         * lower value and therefor the array outcome will be correct.
         */
        $result['default'][$this->generateUniqueKey('default', $result['default'] ?? [], $checkout, $step)] = [
            'handle' => 'hyva_checkout',
        ];

        if ($includeConfigUpdates->canApplyGlobalHandles()) {
            // Apply a default layout handle 'hyva_checkout_{checkout_name_attr}'.
            $result['default'][$this->generateUniqueKey('global', $result['default'] ?? [], $checkout, $step)] = [
                'handle' => implode('_', ['hyva_checkout', $checkoutName]),
            ];
        }

        if ($includeConfigUpdates->canApplyStepHandles()) {
            // Apply a default layout handle 'hyva_checkout_{checkout_name_attr}_{step_name_attr}'.
            $result['default'][$this->generateUniqueKey('step', $result['default'] ?? [], $checkout, $step)] = [
                'handle' => implode('_', ['hyva_checkout', $checkoutName, $stepName]),
            ];
        }

        $includeLayoutUpdate = function (DOMElement $update, DOMElement $checkout, array $result) use ($step): array {
            $type   = $update->getAttribute(self::ATTRIBUTE_IF);
            $method = $update->getAttribute('method');

            $processor = $update->getAttribute('processor');
            $processor = empty($processor) ? 'custom' : $processor;

            $data = [
                'handle' => $update->getAttribute('handle'),
                'type'   => empty($type) ? 'is_always_allow' : $type,
                'method' => empty($method) ? CustomConditionInterface::DEFAULT_METHOD : $method,
            ];

            $key = $this->generateUniqueKey($processor, $result[$processor] ?? [], $checkout, $step, ['handle' => $update->getAttribute('handle')]);

            $result[$processor][$key] = $data;
            // Mark as to be removed on the cleanup round trip.
            $result[$processor][$key]['remove'] = $update->getAttribute('remove') === 'true';

            return $result;
        };

        foreach ($stepLayoutUpdates as $update) {
            $result = $includeLayoutUpdate($update, $checkout, $result);
        }

        return $parent ? $this->merge($this->includeUpdates($step, $parent, $root), $result) : $result;
    }

    /**
     * <checkout>
     *     <step>
     *         <conditions...
     */
    public function includeCustomConditions(
        DOMElement $step,
        DOMElement $checkout
    ): ?array {
        $conditions = $step->getElementsByTagName('condition');
        $parent = $this->lookupAncestor($checkout);
        $result = [];

        /** @var DOMElement $update */
        foreach ($conditions as $condition) {
            $conditionName   = $condition->getAttribute(self::ATTRIBUTE_NAME);
            $conditionIf     = $condition->getAttribute(self::ATTRIBUTE_IF);
            $conditionMethod = $condition->getAttribute('method');
            $conditionRemove = $condition->getAttribute(self::ATTRIBUTE_REMOVE);

            // Fill step evaluator.
            $item = [
                'name'   => $conditionName,
                'method' => $conditionMethod,
                'type'   => $conditionIf,
                'remove' => empty($conditionIf) || $conditionRemove === 'true'
            ];

            if (empty($item['method'])) {
                $item['method'] = CustomConditionInterface::DEFAULT_METHOD;
            }

            $conditionBefore = $condition->getAttribute(self::ATTRIBUTE_BEFORE);
            $conditionAfter  = $condition->getAttribute(self::ATTRIBUTE_AFTER);

            // Fill optional before/after siblings.
            if (! empty($conditionBefore)) {
                $item[self::ATTRIBUTE_BEFORE] = $conditionBefore;
            } elseif (! empty($conditionAfter)) {
                $item[self::ATTRIBUTE_AFTER] = $conditionAfter;
            }

            $result[$item['name']] = $item;
        }

        return $this->sortWithBeforeAfter(
            $parent ? $this->merge($this->includeCustomConditions($step, $parent), $result) : $result
        );
    }

    /**
     * <checkout>
     *     <step>
     *         <events...
     */
    public function includeObservableEvents(DOMElement $step, DOMElement $checkout): ?array
    {
        $parent = $this->lookupAncestor($checkout);
        $result = [];

        $checkoutName = $checkout->getAttribute(self::ATTRIBUTE_NAME);
        $stepName = $step->getAttribute(self::ATTRIBUTE_NAME);

        $result[] = sprintf('hyva_checkout_%s_%%s', $checkoutName);
        $result[] = sprintf('hyva_checkout_%s_%s_%%s', $checkoutName, $stepName);

        return $parent ? $this->merge($this->includeObservableEvents($step, $parent), $result) : $result;
    }

    /**
     * <checkout>
     *     <sequence...
     *
     * @param DOMElement $checkout
     * @return array
     */
    public function includeCheckoutSequence(DOMElement $checkout): array
    {
        $parent = $this->lookupAncestor($checkout);

        if ($parent instanceof DOMElement) {
            $sequence = $this->includeCheckoutSequence($parent);
        }

        $sequence = $sequence ?? [];
        $sequence[] = $checkout->getAttribute('name');

        return $sequence;
    }

    /**
     * Returns the given checkout parent if it has any.
     */
    protected function lookupAncestor(DOMElement $checkout): ?DOMElement
    {
        $ancestor = $checkout->getAttribute('parent');

        if (empty($ancestor) || ! isset($this->checkouts[$ancestor])) {
            return null;
        }

        return $this->checkouts[$ancestor];
    }

    /**
     * Returns the clone of the given {checkout_name}.{step_name} checkout path.
     */
    protected function lookupStepClone(string $path, array $result): array
    {
        [$cloneCheckoutName, $cloneStepName] = explode('.', $path, 2);

        if (isset($this->checkouts[$cloneCheckoutName])) {
            $cloneCheckout = $this->checkouts[$cloneCheckoutName];
            $cloneMatches = array_filter(
                $this->includeSteps($cloneCheckout),
                fn ($key) => $key === $cloneStepName,
                ARRAY_FILTER_USE_KEY
            );

            if ($cloneMatches[$cloneStepName] ?? null) {
                return $this->merge($cloneMatches[$cloneStepName], $result);
            }
        }

        return $result;
    }

    /**
     * Returns all steps for the given checkout.
     */
    protected function lookupSteps(DOMElement $checkout): array
    {
        $steps  = $checkout->getElementsByTagName('step');
        $parent = $this->lookupAncestor($checkout);
        $result = [];

        foreach ($steps as $step) {
            $result[$step->getAttribute('name')] = $step;
        }

        if ($parent) {
            $result = $this->merge($this->lookupSteps($parent), $result);
        }

        return $result;
    }

    /**
     * Finalize steps after all checkouts are successfully converted.
     */
    protected function finalizeStep(array $step, DOMElement $checkout, int &$position): array
    {
        $step['position'] = $position++;

        /*
         * At this point, we are in the final stage of converting the checkout with all its steps.
         * The "layout" update handle can be in two stages at this point.
         *
         * A. Handle is already set on the step, only the prefix has to be added.
         * B. Handle is set to null where we have to search for the layout set on the checkout of its parent.
         */
        $step['updates']['layout']['handle'] = implode('_', [
            'hyva_checkout_layout',
            $step['updates']['layout']['handle'] ?? $this->getFirstInLine('layout', $checkout, self::LAYOUT_HANDLE_FALLBACK)
        ]);

        $this->removeElements($step);
        return $step;
    }

    protected function removeElements(&$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->removeElements($value);
            } else {
                if (in_array('remove', $array)) {
                    $array = array_filter($array, fn ($value) => $value !== 'remove');
                    return;
                }

                if ($key === 'remove') {
                    if ($value === true) {
                        unset($array);
                    } elseif ($value === false) {
                        unset($array['remove']);
                    }

                    return;
                }
            }
        }

        $array = array_filter($array, function ($value) {
            return ! ($value === true || (is_array($value) && isset($value['remove']) && $value['remove'] === true));
        });
    }

    /**
     * Returns bool based on system configuration.
     */
    protected function ifconfig(string $path): bool
    {
        $result = $this->scopeConfig->isSetFlag(ltrim($path, '!'), ScopeInterface::SCOPE_STORE);
        return $path[0] === '!' ? ! $result : $result;
    }

    /**
     * Search for a "checkout" attribute recursively from the inside out.
     */
    protected function getFirstInLine(string $key, DOMElement $checkout, $default = null): ?string
    {
        // Return the default value when there are no checkouts set at this point.
        if (empty($this->checkouts)) {
            return $default;
        }

        $search = $checkout->getAttribute($key);
        $search = empty($search) ? null : $search;

        if ($search) {
            return $search;
        }

        $parent = $checkout->getAttribute('parent');
        $parent = empty($parent) ? null : $this->checkouts[$parent] ?? null;

        if ($parent === null) {
            return $default;
        }

        return $this->getFirstInLine($key, $parent, $default);
    }

    protected function sortWithBeforeAfter(
        array $items,
        ?string $attributeName = null,
        ?string $attributeBefore = null,
        ?string $attributeAfter = null,
        bool $removeAttribute = true
    ): array {
        $sorting = [];

        $attributeName   = $attributeName   ?? self::ATTRIBUTE_NAME;
        $attributeBefore = $attributeBefore ?? self::ATTRIBUTE_BEFORE;
        $attributeAfter  = $attributeAfter  ?? self::ATTRIBUTE_AFTER;

        foreach ($items as $key => $item) {
            if (isset($item[$attributeBefore])) {
                $position = $item[$attributeBefore] === '-' ? null : array_search($item[$attributeBefore], $sorting, true);
                array_splice($sorting, $position ?: 0, 0, $item[$attributeName]);

                if ($removeAttribute) {
                    unset($items[$key][$attributeBefore]);
                }
            } elseif (isset($item[$attributeAfter])) {
                $position = $item[$attributeAfter] === '-' ? null : array_search($item[$attributeAfter], $sorting, true);
                array_splice($sorting, (($position ?: count($items)) + 1), 0, $item[$attributeName]);

                if ($removeAttribute) {
                    unset($items[$key][$attributeAfter]);
                }
            } else {
                $sorting[] = is_array($item) ? $item[$attributeName] : $item;
            }
        }

        // Final sibling sorting.
        return array_replace(array_fill_keys($sorting, null), $items);
    }

    /**
     * Recursive array type value merging.
     *
     * In these cases $array1 is leading where values different from a type array
     * should not be overwritten.
     *
     * All other types will be left as they are where the 'else' statement handles
     * optional double keys and appends those with a new key when this is the case.
     */
    protected function merge(array $array1, array $array2): array
    {
        foreach ($array2 as $key => $value) {
            if (is_array($value)) {
                $array1[$key] = isset($array1[$key]) ? $this->merge($array1[$key], $value) : $value;
            } else {
                if (empty($value)) {

                    // Continue with the parent.
                    continue;

                }

                if (is_string($key) && $value === self::ATTRIBUTE_REMOVE) {

                    // Remove from the parent by a sibling.
                    unset($array1[$key]);

                } elseif (is_numeric($key)) {

                    // Key could already exist thanks to the parent.
                    $array1[] = $value;

                } else {

                    // Copy the value and preserve the current key.
                    $array1[$key] = $value;

                }
            }
        }

        // We only want to 'array_unique' when all values in $array1 are array typed values.
        return count(array_filter($array1, 'is_array')) === count($array1)
            ? array_unique($array1, SORT_REGULAR)
            : $array1;
    }

    /**
     * Generates a predictable but unique key.
     *
     * @param array $previousItems previously added items to base a count on (empty array allowed).
     */
    protected function generateUniqueKey(string $subject, array $previousItems, DOMElement $checkout, ?DOMElement $step = null, ?array $data = null): string
    {
        /*
         * This function generates a unique hash based on the contents of the given array. It's designed to ensure that
         * multiple children don't have identical payloads, enhancing data integrity and preventing redundancy.
         */
        if ($data) {
            return hash('sha256', $this->jsonSerializer->serialize($data));
        }

        $prefixes[] = $subject;
        $prefixes[] = $checkout->getAttribute(self::ATTRIBUTE_NAME);

        if ($step) {
            $prefixes[] = $step->getAttribute(self::ATTRIBUTE_NAME);
        }

        $prefixes[] = count($previousItems);
        return strtolower(implode('.', $prefixes));
    }
}
