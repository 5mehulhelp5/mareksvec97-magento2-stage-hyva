<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\DataContainer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\Review;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer;
use MageWorx\ReviewReminderBase\Model\EmailTemplateResolver;
use MageWorx\ReviewReminderBase\Model\UnsubscribeUrl;

class EmailDataContainer extends DataContainer
{
    const EMAIL_TEMPLATE_ID = 'email_template_id';

    const REVIEWS = 'reviews';

    /**
     * @var MageWorx\ReviewReminderBase\Model\UnsubscribeUrl
     */
    protected $unsubscribeUrl;

    /**
     * @var EmailTemplateResolver
     */
    protected $emailTemplateResolver;

    /**
     * EmailDataContainer constructor.
     *
     * @param UnsubscribeUrl $unsubscribeUrl
     * @param EmailTemplateResolver $emailTemplateResolver
     * @param array $data
     */
    public function __construct(
        UnsubscribeUrl $unsubscribeUrl,
        EmailTemplateResolver $emailTemplateResolver,
        array $data = []
    ) {
        parent::__construct($data);
        $this->unsubscribeUrl        = $unsubscribeUrl;
        $this->emailTemplateResolver = $emailTemplateResolver;
    }

    /**
     * @param string $emailTemplateId
     * @return EmailDataContainer
     */
    public function setEmailTemplateId(string $emailTemplateId): EmailDataContainer
    {
        return $this->setData(self::EMAIL_TEMPLATE_ID, $emailTemplateId);
    }

    /**
     * @return string|null
     */
    public function getEmailTemplateId(): ?string
    {
        return $this->getData(self::EMAIL_TEMPLATE_ID);
    }

    /**
     * @return string|null
     * @throws \Exception
     */
    public function getFinalEmailTemplateId(): ?string
    {
        return $this->emailTemplateResolver->resolveTemplate($this);
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getUnsubscribeUrl(): ?string
    {
        return $this->unsubscribeUrl->getUnsubscribeUrl($this->getStoreId(), $this->getCustomerEmail());
    }

    /**
     * @param Review[] $reviews
     * @return EmailDataContainer
     */
    public function setReviews(array $reviews): EmailDataContainer
    {
        return $this->setData(self::REVIEWS, $reviews);
    }

    /**
     * @return Review[]|null
     */
    public function getReviews(): ?array
    {
        return $this->getData(self::REVIEWS);
    }
}
