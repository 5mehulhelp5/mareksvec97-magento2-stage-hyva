<?php

declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

use Amazon\Payment\Api\Data\PendingAuthorizationInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use MageWorx\XReviewBase\Model\Source\Filter as FilterOptions;

class ConfigProvider
{
    const TOOLBAR_DEFAULT_LIMIT = 10;

    const WIDGET_REVIEW_CHARACTERS_DEFAULT_LIMIT = 71;

    const XML_CONFIG_PATH_ALLOW_IMAGE_UPLOADING = 'mageworx_xreview/main/is_allow_images_uploading';

    const XML_CONFIG_PATH_DISPLAY_IMAGE = 'mageworx_xreview/main/is_display_images_in_review';

    const XML_CONFIG_PATH_IMAGE_SIZE = 'mageworx_xreview/main/image_size';

    const XML_CONFIG_PATH_DISPLAY_RECOMMEND_LABEL = 'mageworx_xreview/main/display_recommend_label';

    const XML_CONFIG_PATH_DISPLAY_HELPFUL = 'mageworx_xreview/main/display_helpful_label';

    const XML_CONFIG_PATH_DISPLAY_VERIFIED_LABEL = 'mageworx_xreview/main/display_verified_label';

    const XML_CONFIG_PATH_DISPLAY_LOCATION = 'mageworx_xreview/main/is_display_location';

    const XML_CONFIG_PATH_LOCATION_TEMPLATE = 'mageworx_xreview/main/location_template';

    const XML_CONFIG_PATH_DISPLAY_PROS_CONS = 'mageworx_xreview/main/display_pros_cons';

    const XML_CONFIG_PATH_DISPLAY_PRIVACY_TOGGLE = 'mageworx_xreview/main/display_privacy_toggle';

    const XML_CONFIG_PATH_PRIVACY_MESSAGE = 'mageworx_xreview/main/privacy_message';

    const XML_CONFIG_PATH_DISPLAY_REWARD_MESSAGE = 'mageworx_xreview/main/display_privacy_toggle';

    const XML_CONFIG_PATH_REWARD_MESSAGE = 'mageworx_xreview/main/reward_message';

    const XML_CONFIG_PATH_REVIEW_CHARACTERS_LIMIT = 'mageworx_xreview/main/review_characters_limit';

    const XML_CONFIG_PATH_WIDGET_REVIEW_CHARACTERS_LIMIT = 'mageworx_xreview/main/widget_review_characters_limit';

    const XML_CONFIG_PATH_TOOLBAR_FILTERS = 'mageworx_xreview/main/toolbar/toolbar_filters';

    const XML_CONFIG_PATH_DEFAULT_SORT_FIELD = 'mageworx_xreview/main/toolbar/default_sort_field';

    const XML_CONFIG_PATH_TOOLBAR_ORDERS = 'mageworx_xreview/main/toolbar/toolbar_sorting';

    const XML_CONFIG_PATH_DEFAULT_SORT_DIRECTION = 'mageworx_xreview/main/toolbar/default_sort_direction';

    const XML_CONFIG_PATH_TOOLBAR_LIMIT = 'mageworx_xreview/main/toolbar/default_toolbar_limit';

    const XML_CONFIG_PATH_TOOLBAR_REMEMBER_PAGINATION = 'mageworx_xreview/main/toolbar/remember_pagination';

    const FILTER_DISABLE = 'off';
    const FILTER_ENABLE  = 'on';

    const MEDIA_FILTER_DISABLE = 'off';
    const MEDIA_FILTER_ENABLE  = 'on';

    const LOCATION_FILTER_DISABLE = 'off';
    const LOCATION_FILTER_ENABLE  = 'on';

    const DEFAULT_FILTER_VALUE          = self::FILTER_DISABLE;
    const DEFAULT_FILTER_MEDIA_VALUE    = self::MEDIA_FILTER_DISABLE;
    const DEFAULT_FILTER_LOCATION_VALUE = self::LOCATION_FILTER_DISABLE;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var FullWidthLayoutRegistry
     */
    protected $fullWidthLayoutRegistry;

    /**
     * ConfigProvider constructor.
     *
     * @param FullWidthLayoutRegistry $fullWidthLayoutRegistry
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        FullWidthLayoutRegistry $fullWidthLayoutRegistry,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig             = $scopeConfig;
        $this->fullWidthLayoutRegistry = $fullWidthLayoutRegistry;
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isAllowImagesUploading(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_ALLOW_IMAGE_UPLOADING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayImages(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return int
     */
    public function getImageSize(): int
    {
        $size = (int)$this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_IMAGE_SIZE
        );

        return ($size === 0) ? 300 : $size;
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayRecommendLabel(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_RECOMMEND_LABEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayHelpful(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_HELPFUL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayVerifiedLabel(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_VERIFIED_LABEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayLocation(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_LOCATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return string
     */
    public function getLocationTemplate(?int $store = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_LOCATION_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayPrivacyToggle(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_PRIVACY_TOGGLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return string
     */
    public function getPrivacyMessage(?int $store = null): string
    {
        return str_replace(
            ["\n", "\r"],
            '',
            (string)$this->scopeConfig->getValue(
                self::XML_CONFIG_PATH_PRIVACY_MESSAGE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            )
        );
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayRewardMessage(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_REWARD_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return string
     */
    public function getRewardMessage(?int $store = null): string
    {
        return str_replace(
            ["\n", "\r"],
            '',
            (string)$this->scopeConfig->getValue(
                self::XML_CONFIG_PATH_REWARD_MESSAGE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            )
        );
    }

    /**
     * @param int|null $store
     * @return int|null
     */
    public function getReviewCharactersLimit(int $store = null): ?int
    {
        $value = $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_REVIEW_CHARACTERS_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value ? (int)$value : null;
    }

    /**
     * @param int|null $store
     * @return int
     */
    public function getWidgetReviewCharactersLimit(int $store = null): int
    {
        $value = $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_WIDGET_REVIEW_CHARACTERS_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value ? (int)$value : self::WIDGET_REVIEW_CHARACTERS_DEFAULT_LIMIT;
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isDisplayProsAndCons(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_DISPLAY_PROS_CONS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return array
     */
    public function getAllowedToolbarFilters(?int $store = null): array
    {
        $toolbarFiltersString = (string)$this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_TOOLBAR_FILTERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $toolbarFilters       = array_filter(explode(',', $toolbarFiltersString));

        return $toolbarFilters;
    }

    /**
     * @param int|null $store
     * @return array
     */
    public function getDefaultSortField(?int $store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_DEFAULT_SORT_FIELD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $store
     * @return array
     */
    public function getAllowedToolbarOrders(?int $store = null): array
    {
        $toolbarSortingString = (string)$this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_TOOLBAR_ORDERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $toolbarSortingList   = array_filter(explode(',', $toolbarSortingString));

        return $toolbarSortingList;
    }

    /**
     * @param int|null $store
     * @return array
     */
    public function getDefaultSortDirection(?int $store = null): string
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_DEFAULT_SORT_DIRECTION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve default per page values
     *
     * @param int|null $store
     * @return string|int
     */
    public function getDefaultLimitPerPageValue(?int $store = null)
    {
        $value = trim(
            $this->scopeConfig->getValue(
                self::XML_CONFIG_PATH_TOOLBAR_LIMIT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            )
        );

        if ($value === 'all') {
            return $value;
        }
        $value = (int)$value;

        if (!$value) {
            $value = self::TOOLBAR_DEFAULT_LIMIT;
        }

        return $value;
    }

    /**
     * @param int|null $store
     * @return string|int
     */
    public function isRememberPagination(?int $store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_CONFIG_PATH_TOOLBAR_REMEMBER_PAGINATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve available limits
     *
     * @param string $mode
     * @return array
     */
    public function getAvailableLimit(?int $store = null)
    {
        $limit = $this->getDefaultLimitPerPageValue($store);

        if ($limit === 'all') {
            return ['all' => __('All')];
        }

        return [$limit => $limit];
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function getIsEnableMediaReviewFilter(?int $store = null): bool
    {
        return in_array(FilterOptions::FILTER_BY_MEDIA, $this->getAllowedToolbarFilters($store));
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function getIsEnableLocationReviewFilter(?int $store = null): bool
    {
        return in_array(FilterOptions::FILTER_BY_LOCATION, $this->getAllowedToolbarFilters($store));
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function getIsEnableCustomerVerifiedReviewFilter(?int $store = null): bool
    {
        return in_array(FilterOptions::FILTER_BY_VERIFIED_CUSTOMER, $this->getAllowedToolbarFilters($store));
    }

    /**
     * Get default filter field
     *
     * @return null|string
     */
    public function getDefaultFilterField()
    {
        return self::DEFAULT_FILTER_VALUE;
    }

    /**
     * Get default media filter field
     *
     * @return null|string
     */
    public function getDefaultMediaFilterField()
    {
        return self::DEFAULT_FILTER_MEDIA_VALUE;
    }

    /**
     * Get default location filter field
     *
     * @return null|string
     */
    public function getDefaultLocationFilterField()
    {
        return self::DEFAULT_FILTER_LOCATION_VALUE;
    }

    /**
     * @return string  'ajax' or 'built-in'
     */
    public function getReviewListRenderTypeForFullWidthPage(): string
    {
        return 'ajax';
    }

    /**
     * @return bool
     */
    public function isForceAjax(): bool
    {
        if ($this->fullWidthLayoutRegistry->getIsFullWidthLayout()
            && $this->getReviewListRenderTypeForFullWidthPage() == 'ajax'
        ) {
            return true;
        }

        return false;
    }
}

