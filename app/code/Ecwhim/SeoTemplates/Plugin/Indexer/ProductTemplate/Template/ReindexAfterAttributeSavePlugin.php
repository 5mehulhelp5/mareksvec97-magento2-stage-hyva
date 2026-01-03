<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Plugin\Indexer\ProductTemplate\Template;

class ReindexAfterAttributeSavePlugin
{
    /**
     * @var CheckProductTemplatesAvailability
     */
    private $checkProductTemplatesAvailability;

    /**
     * ReindexAfterAttributeSavePlugin constructor.
     *
     * @param CheckProductTemplatesAvailability $checkProductTemplatesAvailability
     */
    public function __construct(CheckProductTemplatesAvailability $checkProductTemplatesAvailability)
    {
        $this->checkProductTemplatesAvailability = $checkProductTemplatesAvailability;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $result
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function afterSave(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $subject,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $result
    ): \Magento\Catalog\Model\ResourceModel\Eav\Attribute {
        if ($result->dataHasChangedFor(\Magento\Catalog\Api\Data\EavAttributeInterface::IS_USED_FOR_PROMO_RULES)
            && !$result->getIsUsedForPromoRules()
        ) {
            $this->checkProductTemplatesAvailability->execute($result->getAttributeCode());
        }

        return $result;
    }
}
