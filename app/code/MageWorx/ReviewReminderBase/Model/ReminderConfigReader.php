<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ReminderConfigReader
{
    const CONFIG_PATH_EMAIL_REMINDERS_ENABLED = 'mageworx_reviewreminder/email/enable';

    const CONFIG_PATH_EMAIL_SENDER = 'mageworx_reviewreminder/email/sender_email_identity';

    const CONFIG_PATH_EMAIL_TEMPLATE = 'mageworx_reviewreminder/email/template_review_reminder';

    const CONFIG_PATH_POPUP_REMINDERS_ENABLED = 'mageworx_reviewreminder/popup/enable';

    const CONFIG_PATH_MAX_PRODUCTS_COUNT_IN_POPUP = 'mageworx_reviewreminder/popup/max_products_count_in_popup';

    const CONFIG_PATH_ADD_UTM_PARAMS = 'mageworx_reviewreminder/utm/add_utm_params';

    const CONFIG_PATH_UTM_SOURCE = 'mageworx_reviewreminder/utm/utm_source';

    const CONFIG_PATH_UTM_MEDIUM = 'mageworx_reviewreminder/utm/utm_medium';

    const CONFIG_PATH_UTM_CAMPAIGN = 'mageworx_reviewreminder/utm/utm_campaign';

    const CONFIG_PATH_UTM_CONTENT = 'mageworx_reviewreminder/utm/utm_content';

    const CONFIG_PATH_UTM_TERM = 'mageworx_reviewreminder/utm/utm_term';

    /**
     * scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ReminderConfigReader constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEmailRemindersEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_EMAIL_REMINDERS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isPopupRemindersEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_POPUP_REMINDERS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getNotificationEmailTemplateId($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getMaxProductsCountInPopup($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::CONFIG_PATH_MAX_PRODUCTS_COUNT_IN_POPUP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function getAddUtmParams($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ADD_UTM_PARAMS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getUtmContent($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_UTM_CONTENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getUtmSource($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_UTM_SOURCE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getUtmCampaign($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_UTM_CAMPAIGN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getUtmTerm($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_UTM_TERM,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getUtmMedium($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_PATH_UTM_MEDIUM,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getUtmParams($storeId = null): array
    {
        return array_filter(
            [
                'utm_content'  => $this->getUtmContent($storeId),
                'utm_campaign' => $this->getUtmCampaign($storeId),
                'utm_source'   => $this->getUtmSource($storeId),
                'utm_term'     => $this->getUtmTerm($storeId),
                'utm_medium'   => $this->getUtmMedium($storeId)
            ]
        );
    }

    /**
     * Retrieve period in days
     *
     * @return int
     */
    public function getUntouchablePeriodForCleanup(): int
    {
        return 60;
    }
}
