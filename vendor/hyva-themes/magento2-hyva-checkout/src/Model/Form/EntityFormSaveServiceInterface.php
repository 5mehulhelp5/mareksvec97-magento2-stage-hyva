<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Form;

use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @deprecated has been replaced by AbstractEntityFormSaveService.
 * @see AbstractEntityFormSaveService
 */
interface EntityFormSaveServiceInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function save(EntityFormInterface $form): EntityFormInterface;
}
