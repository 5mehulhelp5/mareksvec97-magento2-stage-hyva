<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Block\Review;

use Magento\Customer\Model\Url;

/**
 * Review form block
 */
class Form extends \Magento\Review\Block\Form
{
    /**
     * @var \MageWorx\XReviewBase\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * Form constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param Url $customerUrl
     * @param \MageWorx\XReviewBase\Model\ConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        Url $customerUrl,
        \MageWorx\XReviewBase\Model\ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $reviewData,
            $productRepository,
            $ratingFactory,
            $messageManager,
            $httpContext,
            $customerUrl,
            $data,
            $serializer
        );

        $this->configProvider = $configProvider;
    }

    /**
     * Initialize review form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        if ($this->isJetTheme()) {
            $this->setTemplate('MageWorx_XReviewBase::review/jet_theme_form.phtml');
        } else {
            $this->setTemplate('MageWorx_XReviewBase::review/form.phtml');
        }
    }

    /**
     * @return bool
     */
    protected function isJetTheme()
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
    public function isAllowImagesUploading()
    {
        return $this->configProvider->isAllowImagesUploading();
    }

    /**
     * @return bool
     */
    public function isAllowRecommend()
    {
        return $this->configProvider->isDisplayRecommendLabel();
    }

    /**
     * @return bool
     */
    public function isAllowProsAndCons()
    {
        return $this->configProvider->isDisplayProsAndCons();
    }

    /**
     * @return bool
     */
    public function isAllowPolicyField()
    {
        return $this->configProvider->isDisplayPrivacyToggle();
    }

    /**
     * @return string
     */
    public function getPolicyMessage()
    {
        return $this->configProvider->getPrivacyMessage();
    }
}
