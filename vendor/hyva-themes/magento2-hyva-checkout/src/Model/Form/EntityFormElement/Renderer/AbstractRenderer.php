<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement\Renderer;

use Hyva\Checkout\Model\Form\AbstractEntityFormElement;
use Hyva\Checkout\Model\Form\EntityFieldInterface;
use Hyva\Checkout\Model\Form\EntityFormElement\RendererInterface;
use Hyva\Checkout\Model\Form\EntityFormElementInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Psr\Log\LoggerInterface;

abstract class AbstractRenderer implements RendererInterface
{
    // Layout XML renders container.
    public const LAYOUT_ELEMENT_RENDERERS = 'entity-form.element-renderers';

    /** @deprecated global layout elements have been replaced with block names prefixed by "accessory.". */
    public const LAYOUT_ELEMENT_LABEL = 'entity-form.element-label';
    public const LAYOUT_ELEMENT_COMMENT = 'entity-form.element-comment';

    protected Layout $layout;
    protected LoggerInterface $logger;

    public function __construct(
        Layout $layout,
        LoggerInterface $logger
    ) {
        $this->layout = $layout;
        $this->logger = $logger;
    }

    public function render(EntityFormElementInterface $element, ?AbstractBlock $block = null): string
    {
        try {
            $block ??= $this->resolveBlock($element);

            if ($block === false) {
                throw new NotFoundException(
                    __('Element renderer for "%1" not found in "%2"', $element->getId(), static::LAYOUT_ELEMENT_RENDERERS)
                );
            }
        } catch (NotFoundException $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);

            return '';
        }

        /** @deprecated The "field" parameter is still available, but it is recommended to use "element" instead. */
        $block->setData('field', $element);
        $block->setData('element', $element);

        return $block->toHtml();
    }

    /**
     * @deprecated use method renderWithTemplate instead.
     */
    public function renderAs(string $alias, EntityFormElementInterface $element): string
    {
        $element = clone $element;
        $element->setData('layout_alias', $alias);

        return $this->render($element);
    }

    public function renderWithTemplate(string $template, AbstractEntityFormElement $element): string
    {
        $element = clone $element;

        try {
            $block = $this->resolveBlock($element);

            if ($block === false) {
                throw new NotFoundException(
                    __('Element renderer for "%1" not found in "%2"', $element->getId(), static::LAYOUT_ELEMENT_RENDERERS)
                );
            }

            if ($block->getTemplate() !== $template) {
                $block->setTemplate($template);
            }
        } catch (NotFoundException $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);

            return '';
        }

        return $this->render($element);
    }

    /**
     * Attempts to render the content within the block or container associated with the "label" alias.
     */
    public function renderLabel(EntityFormElementInterface $element): string
    {
        return $this->renderAccessory($element, 'label', self::LAYOUT_ELEMENT_LABEL);
    }

    /**
     * Attempts to render the content within the block or container associated with the "tooltip" alias.
     */
    public function renderTooltip(EntityFormElementInterface $element): string
    {
        return $this->renderAccessory($element, 'tooltip');
    }

    /**
     * Attempts to render the content within the block or container associated with the "hint-text" alias.
     */
    public function renderHintText(EntityFormElementInterface $element): string
    {
        return $this->renderAccessory($element, 'hint-text');
    }

    /**
     * Attempts to render the content within the block or container associated with the "comment" alias.
     */
    public function renderComment(EntityFormElementInterface $element): string
    {
        return $this->renderAccessory($element, 'comment');
    }

    /**
     * Attempts to render the content within the block or container associated with the "icon" alias.
     */
    public function renderIcon(AbstractEntityFormElement $element): string
    {
        return $this->renderAccessory($element, 'icon');
    }

    /**
     * Attempts to render the content within the block or container associated with the "before" alias.
     */
    public function renderBefore(EntityFormElementInterface $element): string
    {
        return $this->renderAccessory($element, 'before');
    }

    /**
     * Attempts to render the content within the block or container associated with the "after" alias.
     */
    public function renderAfter(EntityFormElementInterface $element): string
    {
        return $this->renderAccessory($element, 'after');
    }

    /**
     * Identify the singular block associated with the given element, taking into account available render types.
     * Includes a fallback mechanism to resolve a required template by traversing downward through the types.
     *
     * @param callable|null $filter @deprecated this no longer applies after implementing the default fallback mechanism.
     */
    public function resolveBlock(EntityFormElementInterface $element, ?callable $filter = null)
    {
        $renderers = $this->getRenderersBlock();

        if (! $renderers) {
            return false;
        }

        /** @var string|null $renderTemplateBackup */
        $renderTemplateBackup = null;
        /** @var Template|false $renderTypeBlock */
        $renderTypeBlock = false;

        /*
         * Attempting to find a block element in the layout, emphasizing a fallback system.
         * If a specialized block type isn't detected, it defaults to a text-based block.
         *
         * The 'renderer candidate' may serve as a reference without an assigned template. Consequently,
         * the code iterates through available fallback children to locate a suitable template. This template
         * becomes the final choice for rendering the discovered block.
         */
        foreach ($this->getRenderTypes($element) as $renderType) {
            if ($renderer = $renderers->getChildBlock($renderType)) {
                // The search concludes as all necessary elements have been found.
                if ($renderTypeBlock && $renderTemplateBackup) {
                    break;
                }

                // Assigns and retains the initial discovered render type template within the list.
                if (! empty($renderer->getTemplate()) && ! $renderTemplateBackup) {
                    $renderTemplateBackup = $renderer->getTemplate();
                }

                // Proceeds with the execution if a render type block has been previously identified.
                if ($renderTypeBlock) {
                    continue;
                }

                $renderTypeBlock = $renderer;
            }
        }

        if ($renderTypeBlock && empty($renderTypeBlock->getTemplate())) {
            $renderTypeBlock->setTemplate(
                $renderTemplateBackup ?? $this->getDefaultTemplateForElement($element)
            );
        }

        return $renderTypeBlock;
    }

    /**
     * Determine and resolve all blocks related to the given element, considering the available render types.
     *
     * @throws NotFoundException
     * @return array<int, Template>
     */
    public function resolveAccessoryBlocks(EntityFormElementInterface $element): array
    {
        /** @var AbstractBlock|false $renderers */
        $renderers = $this->layout->getBlock(static::LAYOUT_ELEMENT_RENDERERS);

        if ($renderers === false) {
            throw new NotFoundException(
                __('Renderers block "%1" not found', static::LAYOUT_ELEMENT_RENDERERS)
            );
        }

        return array_filter(
            array_map(fn ($rendererType) => $renderers->getChildBlock($rendererType), $this->getRenderTypes($element))
        );
    }

    /**
     * Determines and returns the appropriate render types by searching from specific to generic.
     *
     * @return array<int, string>
     */
    public function getRenderTypes(EntityFormElementInterface $element, ?string $alias = null): array
    {
        $alias ??= $element->getLayoutAlias() ?? $element->getId();

        return array_unique([
            $element->getForm()->getNamespace() . '.' . $alias,
            $alias
        ]);
    }

    /**
     * Attempts to render the accessory block of the element, with a fallback to rendering the container if it exists.
     *
     * Example:
     *   // Get a aliased label, tooltip or comment child block or searches on a global level for "accessory.tooltip".
     *   ...->renderAccessory($element, 'tooltip')
     *
     * @param string $target the accessory target to be searched for in relation to the element.
     * @param string|null $global @deprecated replaced by specific layout XML block name prefixes e.g. "accessory.".
     */
    public function renderAccessory(EntityFormElementInterface $element, string $target, ?string $global = null): string
    {
        try {
            return $this->resolveAccessory($element, $target)->toHtml();
        } catch (NotFoundException $exception) {
            /*
             * Maintains backward compatibility for $global parameters.
             *
             * In versions prior to 1.1.11, a global layout block name could be passed, which would be attempted to
             * be retrieved from the loaded layout. Since version 1.1.11, this functionality has been relocated to the
             * option of binding a specific element to an "accessory"-prefixed block or container inside the assigned
             * block renderer. The specific block renderer is specified by the LAYOUT_ELEMENT_RENDERERS constant.
             */
            if ($global) {
                $global = $this->layout->getBlock($global);

                if ($global) {
                    $global->setData('element', $element);

                    return $global->toHtml();
                }
            }

            return $this->renderAccessoryContainer($element, $target);
        }
    }

    /**
     * Attempts to render the accessory container if it exists.
     *
     * @param string $target the accessory container target to be searched for in relation to the element.
     */
    public function renderAccessoryContainer(EntityFormElementInterface $element, string $target): string
    {
        try {
            return $this->resolveAccessory($element, $target)->toHtml();
        } catch (NotFoundException $exception) {
            try {
                $parent = $this->resolveBlock($element);

                if (! $parent) {
                    throw new NotFoundException(
                        __('Element %1 could not be resolved while searching for %2', $element->getId(), $target)
                    );
                }

                return $parent->getChildHtml($target);
            } catch (NotFoundException $exception) {
                return '';
            }
        }
    }

    public function getDefaultTemplateForElement(AbstractEntityFormElement $element): string
    {
        return $element instanceof EntityFieldInterface
            ? 'Hyva_Checkout::form/field/text.phtml'
            : 'Hyva_Checkout::form/element/html/not-found.phtml';
    }

    /**
     * @throws NotFoundException
     *
     * @param string $target the accessory target to be searched for in relation to the element.
     */
    protected function resolveAccessory(EntityFormElementInterface $element, string $target): AbstractBlock
    {
        $types = $this->resolveAccessoryBlocks($element);

        if (empty($types)) {
            throw new NotFoundException(
                __('No %1 accessory found for element %2', $target, $element->getId())
            );
        }

        $parent = null;
        $accessory = null;

        // Search for children aliased with the $target such as 'label', 'comment' etc.
        foreach ($types as $block) {
            $parent = $block;
            $accessory = $block->getChildBlock($target);

            // Stop searching when the accessory was found.
            if ($accessory) {
                break;
            }
        }

        // If no accessory is found at the current level, attempt to locate it globally.
        if (! $accessory) {
            $accessory = $this->getRenderersBlock('accessory.' . $target);
        }

        // If it wasn't found globally either, throw an exception.
        if (! $accessory) {
            throw new NotFoundException(
                __('No %1 accessory found for element %2', $target, $element->getId())
            );
        }

        if (! $accessory instanceof DataObject) {
            return $accessory;
        }

        $accessory->setData('element', $element);

        if ($parent) {
            $accessory->setData('parent', $parent);
        }

        return $accessory;
    }

    /**
     * Retrieves the block responsible for containing all form elements, with the option
     * to directly retrieve a child block by name if the parent block is found.
     *
     * If a specific child block name is specified, only that child of the parent container
     * block will be returned when it exists.
     *
     * @return false|AbstractBlock
     */
    protected function getRenderersBlock(?string $child = null)
    {
        $block = $this->layout->getBlock(static::LAYOUT_ELEMENT_RENDERERS);

        if ($child) {
            return $block->getChildBlock($child);
        }

        return $block;
    }
}
