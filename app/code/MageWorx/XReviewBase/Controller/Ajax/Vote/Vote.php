<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\XReviewBase\Controller\Ajax\Vote;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Psr\Log\LoggerInterface;

class Vote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \MageWorx\XReviewBase\Model\ResourceModel\Vote\CollectionFactory
     */
    protected $voteCollectionFactory;

    /**
     * @var \MageWorx\GeoIP\Helper\Customer
     */
    protected $ipDetector;

    /**
     * @var \MageWorx\XReviewBase\Model\ResourceModel\Vote
     */
    protected $voteResource;

    /**
     * Vote constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \MageWorx\XReviewBase\Model\ResourceModel\Vote\CollectionFactory $voteCollectionFactory
     * @param \MageWorx\XReviewBase\Model\ResourceModel\Vote $voteResource
     * @param \MageWorx\GeoIP\Helper\Customer $ipDetector
     * @param ResultJsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \MageWorx\XReviewBase\Model\ResourceModel\Vote\CollectionFactory $voteCollectionFactory,
        \MageWorx\XReviewBase\Model\ResourceModel\Vote $voteResource,
        \MageWorx\GeoIP\Helper\Customer $ipDetector,
        ResultJsonFactory $resultJsonFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->resultJsonFactory     = $resultJsonFactory;
        $this->logger                = $logger;
        $this->formKeyValidator      = $formKeyValidator;
        $this->voteCollectionFactory = $voteCollectionFactory;
        $this->ipDetector            = $ipDetector;
        $this->voteResource          = $voteResource;
    }

    /**
     * @return ResultJson|null
     */
    public function execute(): ?ResultJson
    {
        $result = $this->resultJsonFactory->create();

        $ip          = $this->ipDetector->getCustomerIp();
        $action      = $this->getRequest()->getParam('action', null);
        $reviewId    = (int)$this->getRequest()->getParam('id', null);
        $arrayResult = [];

        if (!$action
            || !$reviewId
            || !$ip
            || !$this->getRequest()->isAjax()
            || !$this->formKeyValidator->validate($this->getRequest())
        ) {
            return $result->setData(['error' => __('Something went wrong, please try again later.')]);
        }

        if ($action === 'init') {
            $arrayResult = $this->getArrayResult($reviewId, $ip);
            $result      = $this->resultJsonFactory->create();

            return $result->setData($arrayResult);
        } elseif (in_array($action, ['like', 'dislike'])) {
            try {
                $voteCollection = $this->voteCollectionFactory->create();
                $voteCollection->addFieldToFilter('review_id', $reviewId);
                $voteCollection->addFieldToFilter('ip_address', $ip);

                if (($action == 'like' && $voteCollection->getItemsByColumnValue('like_count', 1))
                    || ($action == 'dislike' && $voteCollection->getItemsByColumnValue('dislike_count', 1))
                ) {
                    $message = __('You already voted');
                } else {
                    if ($action == 'like') {
                        if ($voteCollection->getItemsByColumnValue('dislike_count', 1)) {
                            foreach ($voteCollection->getItemsByColumnValue('dislike_count', 1) as $vote) {
                                $this->voteResource->delete($vote);
                            }
                        }

                        $vote = $voteCollection->getNewEmptyItem();
                        $vote->setReviewId($reviewId)->setIpAddress($ip)->setLikeCount(1);
                        $this->voteResource->save($vote);
                    }

                    if ($action == 'dislike') {
                        if ($voteCollection->getItemsByColumnValue('like_count', 1)) {
                            foreach ($voteCollection->getItemsByColumnValue('like_count', 1) as $vote) {
                                $this->voteResource->delete($vote);
                            }
                        }

                        /** @var \MageWorx\XReviewBase\Model\Vote $vote */
                        $vote = $voteCollection->getNewEmptyItem();
                        $vote->setReviewId($reviewId)->setIpAddress($ip)->setDislikeCount(1);
                        $this->voteResource->save($vote);
                    }
                    $message = __('Thank you!');
                }

                $arrayResult = $this->getArrayResult($reviewId, $ip, $message);

                return $this->resultJsonFactory->create()->setData($arrayResult);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $arrayResult = [
                    'error' => __('There was an error loading vote data.')
                ];
                $result->setHttpResponseCode(500);
            }
        }

        return $result->setData($arrayResult);
    }

    /**
     * @param int $reviewId
     * @param string $ip
     * @param string $message
     * @param string $type
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getArrayResult($reviewId, $ip, $message = true, $type = 'success')
    {
        return [
            'overall_data'  => $this->voteResource->getOverallVotes($reviewId),
            'personal_data' => $this->voteResource->getPersonalVotes($reviewId, $ip),
            $type           => $message,
        ];
    }
}
