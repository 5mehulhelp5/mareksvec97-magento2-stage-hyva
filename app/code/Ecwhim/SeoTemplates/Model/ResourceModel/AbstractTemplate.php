<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Model\ResourceModel;

use Ecwhim\SeoTemplates\Api\Data\TemplateInterface;
use Magento\Framework\Stdlib\DateTime;

abstract class AbstractTemplate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateModel;

    /**
     * AbstractTemplate constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateModel
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateModel,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);

        $this->entityManager = $entityManager;
        $this->dateModel     = $dateModel;
    }

    /**
     * @inheritDoc
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * @param TemplateInterface $template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterApplicationProcess(TemplateInterface $template): void
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            [TemplateInterface::APPLICATION_TIME => $this->dateModel->gmtDate(DateTime::DATETIME_PHP_FORMAT)],
            [TemplateInterface::TEMPLATE_ID . ' = ?' => $template->getTemplateId()]
        );
    }
}
