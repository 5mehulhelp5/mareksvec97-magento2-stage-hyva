<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\XReviewBase\Block\Review\Customer\Product;

class Review extends \Magento\Review\Block\Customer\View
{
    /**
     * @var \MageWorx\XReviewBase\Model\ConfigProvider
     */
    protected $configProvider;
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Review constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \MageWorx\XReviewBase\Model\ConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \MageWorx\XReviewBase\Model\ConfigProvider $configProvider,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productRepository,
            $reviewFactory,
            $voteFactory,
            $ratingFactory,
            $currentCustomer,
            $data
        );
        $this->configProvider = $configProvider;
        $this->serializer     = $serializer;
    }

    /**
     * Customer view template name
     *
     * @var string
     */
    protected $_template = 'MageWorx_XReviewBase::review/customer/product/review.phtml';

    /**
     * @return bool
     */
    public function isEnableProsAndCons()
    {
        return $this->configProvider->isDisplayProsAndCons()
            && ($this->getReviewData()->getPros() || $this->getReviewData()->getCons());
    }

    /**
     * @return bool
     */
    public function allowMediaGallery()
    {
        return $this->configProvider->isDisplayImages() && $this->getReviewData()->getMediaGallery();
    }

    /**
     * @return bool
     */
    public function isDisplayHelpful()
    {
        return $this->configProvider->isDisplayHelpful();
    }

    /**
     * @return string
     */
    public function getJsonVoteConfig(): string
    {
        return $this->serializer->serialize($this->getVoteConfig());
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
}
