<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class EavAttributeFormFieldsSerialized extends ArraySerialized
{
    protected Json $serializer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ?Json $serializer = null
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data, $serializer);

        $this->serializer = $serializer
            ?: ObjectManager::getInstance()->get(Json::class);
    }

    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            try {
                $this->setValue(empty($value) ? false : $this->unserialize($value));
            } catch (\Exception $exception) {
                $this->_logger->critical(
                    sprintf(
                        'Failed to unserialize %s config value. The error is: %s',
                        $this->getPath(),
                        $exception->getMessage()
                    ),
                    ['exception' => $exception]
                );
                $this->setValue(false);
            }
        }
    }

    public function unserialize(string $value)
    {
        $rows = $this->serializer->unserialize($value);

        foreach ($rows as $id => $row) {
            if (! isset($row['auto_save'])) {
                $rows[$id]['auto_save'] = '0';
            }

            if (! isset($row['tool_tip'])) {
                $rows[$id]['tool_tip'] = '';
            }
        }

        return $rows;
    }
}
