<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityFormElement;

use Hyva\Checkout\Model\Form\EntityField\EavAttributeMappingConfigInterface;
use Hyva\Checkout\Model\Form\EntityFieldConfigInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * This is a dependency container, so we can change the requirements of abstract form element classes without
 * breaking backwards compatibility.
 *
 * Do not use this class in custom code. If you need to implement a form element and override the __construct,
 * just pass this object on to the parent constructor.
 *
 * This class contains the dependencies of
 * - \Hyva\Checkout\Model\Form\AbstractEntityFormElement
 * - \Hyva\Checkout\Model\Form\EntityField\AbstractEntityField
 * - \Hyva\Checkout\Model\Form\EntityField\EavAttributeField
 *
 * @internal
 */
class FormElementDependencies
{
    private RendererInterface $renderer;
    private EntityFormInterface $form;
    private EntityFieldConfigInterface $fieldConfig;
    private ScopeConfigInterface $scopeConfig;
    private AttributeInterface $attribute;
    private EavAttributeMappingConfigInterface $mappingConfig;

    public function __construct(
        RendererInterface $renderer,
        EntityFormInterface $form,
        EntityFieldConfigInterface $fieldConfig,
        ScopeConfigInterface $scopeConfig,
        AttributeInterface $attribute,
        EavAttributeMappingConfigInterface $mappingConfig
    ) {
        $this->renderer = $renderer;
        $this->form = $form;
        $this->fieldConfig = $fieldConfig;
        $this->scopeConfig = $scopeConfig;
        $this->attribute = $attribute;
        $this->mappingConfig = $mappingConfig;
    }

    public function getRenderer(): RendererInterface
    {
        return $this->renderer;
    }

    public function getForm(): EntityFormInterface
    {
        return $this->form;
    }

    public function getFieldConfig(): EntityFieldConfigInterface
    {
        return $this->fieldConfig;
    }

    public function getScopeConfig(): ScopeConfigInterface
    {
        return $this->scopeConfig;
    }

    public function getAttribute(): AttributeInterface
    {
        return $this->attribute;
    }

    public function setAttribute(AttributeInterface $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function getMappingConfig(): EavAttributeMappingConfigInterface
    {
        return $this->mappingConfig;
    }
}
