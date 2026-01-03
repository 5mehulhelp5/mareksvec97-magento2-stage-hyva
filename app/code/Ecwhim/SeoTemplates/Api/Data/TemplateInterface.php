<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Api\Data;

/**
 * @api
 */
interface TemplateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TEMPLATE_ID      = 'template_id';
    const NAME             = 'name';
    const IS_ACTIVE        = 'is_active';
    const SCOPE            = 'scope';
    const STORE_IDS        = 'store_ids';
    const TYPE             = 'type';
    const CONTENT          = 'content';
    const APPLY_BY_CRON    = 'apply_by_cron';
    const PRIORITY         = 'priority';
    const APPLICATION_TIME = 'application_time';

    /**
     * @return int|null
     */
    public function getTemplateId(): ?int;

    /**
     * @param int $id
     * @return void
     */
    public function setTemplateId(int $id): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;

    /**
     * @return int
     */
    public function getIsActive(): int;

    /**
     * @param int $isActive
     * @return void
     */
    public function setIsActive(int $isActive): void;

    /**
     * @return string
     */
    public function getScope(): string;

    /**
     * @param string $scope
     * @return void
     */
    public function setScope(string $scope): void;

    /**
     * @return int[]
     */
    public function getStoreIds(): array;

    /**
     * @param int[] $storeIds
     * @return void
     */
    public function setStoreIds(array $storeIds): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void;

    /**
     * @return int
     */
    public function getApplyByCron(): int;

    /**
     * @param int $applyByCron
     * @return void
     */
    public function setApplyByCron(int $applyByCron): void;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param int $priority
     * @return void
     */
    public function setPriority(int $priority): void;

    /**
     * @return string
     */
    public function getApplicationTime(): string;

    /**
     * @param string $applicationTime
     * @return void
     */
    public function setApplicationTime(string $applicationTime): void;
}
