<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Block\Review\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ReviewSummaryFactory;
use MageWorx\XReviewBase\Model\ConfigProvider;
use MageWorx\XReviewBase\Model\ResourceModel\Review;

class ListView extends \Magento\Review\Block\Product\View\ListView
{
    /**
     * @var Review
     */
    protected $reviewResource;
    /**
     * @var ConfigProvider
     */
    protected $configProvider;
    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $countryCollectionFactory;
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var ReviewSummaryFactory
     */
    private $reviewSummaryFactory;

    /**
     * @var int|null
     */
    protected $reward;

    /**
     * ListView constructor.
     *
     * @param ConfigProvider $configProvider
     * @param Review $reviewResource
     * @param ReviewSummaryFactory $reviewSummaryFactory
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param ModuleManager $moduleManager
     * @param array $data
     */
    public function __construct(
        \MageWorx\XReviewBase\Model\ConfigProvider $configProvider,
        \MageWorx\XReviewBase\Model\ResourceModel\Review $reviewResource,
        ReviewSummaryFactory $reviewSummaryFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        ModuleManager $moduleManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $collectionFactory,
            $data
        );
        $this->reviewSummaryFactory     = $reviewSummaryFactory;
        $this->reviewResource           = $reviewResource;
        $this->configProvider           = $configProvider;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->formKey                  = $formKey;
        $this->moduleManager            = $moduleManager;
    }

    /**
     * Get collection of reviews
     *
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        parent::getReviewsCollection();

        if ($this->configProvider->isDisplayImages()) {
            $this->_reviewsCollection->setFlag('mageworx_need_media', true);
        }

        if ($this->configProvider->isDisplayLocation() && $this->configProvider->getLocationTemplate()) {
            $this->_reviewsCollection->setFlag('mageworx_need_location_text', true);
        }

        return $this->_reviewsCollection;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRatings()
    {
        $result     = [];
        $statistics = $this->getRawStatistics();
        $statistics = array_combine(array_column($statistics, 'stage_value'), $statistics);

        $reviewCount = array_sum(array_column($statistics, 'stage_count'));

        if (!$reviewCount) {
            return $result;
        }

        for ($i = 1; $i < 6; $i++) {
            $result[(string)$i] = [
                'stage_value'   => (string)$i,
                'stage_count'   => 0,
                'stage_reviews' => ''
            ];
        }

        $suffixes = ['1' => 'one', '2' => 'two', '3' => 'three', '4' => 'four', '5' => 'five'];

        foreach ($result as $stageValue => &$datum) {
            if (!empty($statistics[$stageValue])) {
                $datum = $statistics[$stageValue];
            }

            $datum['style_suffix'] = $suffixes[$stageValue];

            if ($stageValue === '5') {
                $datum['percent'] = 100 - array_sum(array_column($result, 'percent'));
            } else {
                $datum['percent'] = round($datum['stage_count'] / $reviewCount * 100);
            }
        }

        return \array_reverse($result);
    }

    public function getReviewAverageRating()
    {
        $statistics = $this->getRawStatistics();
    }

    /**
     * ['reviews_count' => int, 'rating_summary' => double]
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOverallRating()
    {
        if ($this->getProduct()->getRatingSummary() === null) {
            $this->reviewSummaryFactory->create()->appendSummaryDataToObject(
                $this->getProduct(),
                $this->_storeManager->getStore()->getId()
            );
        }

        return [
            'reviews_count'  => $this->getProduct()->getData('reviews_count'),
            'rating_summary' => round($this->getProduct()->getData('rating_summary') / 20, 1)
        ];
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getRawStatistics()
    {
        if (!$this->getProductRatingStatistics()) {
            $this->setProductRatingStatistics($this->reviewResource->getProductRatingStatistics($this->getProductId()));
        }

        return $this->getProductRatingStatistics();
    }

    protected function _prepareLayout()
    {
        if (!$this->getProduct()) {
            return $this;
        }

        parent::_prepareLayout();

        $toolbar = $this->getLayout()->getBlock('product_review_list.extended_toolbar');

        if ($toolbar) {
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }

    /**
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    public function getToolbar()
    {
        return $this->getChildBlock('toolbar');
    }

    /**
     * @return \Magento\Review\Block\Product\View\ListView
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    /**
     * @param $review
     * @return bool
     */
    public function allowLocation($review)
    {
        return $this->configProvider->isDisplayLocation() && $review->getLocationText();
    }

    /**
     * @param $review
     * @return bool
     */
    public function allowVerifiedLabel($review)
    {
        return $this->configProvider->isDisplayVerifiedLabel() && $review->getIsVerified();
    }

    /**
     * @param $review
     * @return bool
     */
    public function allowRecommendLabel($review)
    {
        return $this->configProvider->isDisplayRecommendLabel() && $review->getIsRecommend();
    }

    /**
     * @param $review
     * @return bool
     */
    public function isEnableProsAndCons($review)
    {
        return $this->configProvider->isDisplayProsAndCons()
            && ($review->getPros() || $review->getCons());
    }

    /**
     * @return bool
     */
    public function isDisplayHelpful()
    {
        return $this->configProvider->isDisplayHelpful();
    }

    /**
     * @param $review
     * @return bool
     */
    public function allowMediaGallery($review)
    {
        return $this->configProvider->isDisplayImages() && $review->getMediaGallery();
    }

    /**
     * @return string
     */
    public function getJsonVoteConfig(): string
    {
        return $this->_jsonEncoder->encode($this->getVoteConfig());
    }

    /**
     * @return array
     */
    public function getVoteConfig(): array
    {
        return [
            'ajaxUrl' => $this->getAjaxUrl(),
        ];
    }

    /**
     * @return string
     */
    protected function getAjaxUrl(): string
    {
        return $this->getUrl('mageworx_xreviewbase/ajax_vote/vote');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return bool
     */
    public function isDisplayRewardMessage()
    {
        if ($this->configProvider->isDisplayRewardMessage()
            && strpos($this->configProvider->getRewardMessage(), '[review_points]') !== false
            && $this->moduleManager->isEnabled('MageWorx_RewardPoints')
        ) {
            $reward = $this->getReward();

            if ($reward) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRewardMessage()
    {
        return str_replace(
            '[review_points]',
            '<span class="mw-text--variation-strong mw-text--highlighted mw-text--highlighted-green">' .
            __('%1 points', $this->getReward()) .
            '</span>',
            $this->configProvider->getRewardMessage()
        );
    }

    /**
     * @return double
     */
    public function getReward()
    {
        if ($this->reward === null) {
            $container = new \Magento\Framework\DataObject();
            $this->_eventManager->dispatch(
                'mageworx_xreviewbase_before_show_reward_message',
                [
                    'container' => $container
                ]
            );

            $this->reward = (int)$container->getReward();
        }

        return $this->reward;
    }

    /**
     * @return int|null
     */
    public function getReviewCharactersLimit(): ?int
    {
        return $this->configProvider->getReviewCharactersLimit();
    }

    /**
     * @return bool
     */
    public function isJetThemeCompatibilityNeeded(): bool
    {
        if (!empty($this->data['skip_jet_theme_compatibility']) && $this->data['skip_jet_theme_compatibility']) {
            return false;
        }

        return $this->isJetTheme();
    }

    /**
     * @return bool
     */
    protected function isJetTheme(): bool
    {
        foreach ($this->_design->getDesignTheme()->getInheritedThemes() as $theme) {
            if ('Amasty/JetTheme' === $theme->getCode()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isFormLinkAllowed(): bool
    {
        if ($this->isJetThemeCompatibilityNeeded()) {
            return false;
        }

        return true;
    }
}
