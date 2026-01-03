<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form\EntityField;

use Hyva\Checkout\Model\Form\EntityFormElement\FormElementDependencies;

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
class FormFieldDependencies extends FormElementDependencies
{
}
