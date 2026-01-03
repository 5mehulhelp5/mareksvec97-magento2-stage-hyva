<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Controller\Manage;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ReviewReminderBase\Api\Data\UnsubscribedInterface;
use MageWorx\ReviewReminderBase\Api\UnsubscribedRepositoryInterface;
use MageWorx\ReviewReminderBase\Model\UnsubscribedFactory;
use MageWorx\ReviewReminderBase\Model\UnsubscribeUrl;

class Unsubscribe extends Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var UnsubscribedFactory
     */
    protected $unsubscribedFactory;
    /**
     * @var UnsubscribedRepositoryInterface
     */
    protected $unsubscribedRepository;

    /**
     * @var UnsubscribeUrl
     */
    protected $unsubscribeUrl;

    /**
     * Unsubscribe constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param UnsubscribedRepositoryInterface $unsubscribedRepository
     * @param UnsubscribedFactory $unsubscribedFactory
     * @param UnsubscribeUrl $unsubscribeUrl
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        UnsubscribedRepositoryInterface $unsubscribedRepository,
        UnsubscribedFactory $unsubscribedFactory,
        UnsubscribeUrl $unsubscribeUrl
    ) {
        parent::__construct($context);
        $this->storeManager           = $storeManager;
        $this->customerSession        = $customerSession;
        $this->unsubscribedFactory    = $unsubscribedFactory;
        $this->unsubscribedRepository = $unsubscribedRepository;
        $this->unsubscribeUrl         = $unsubscribeUrl;
    }

    /**
     * Save review reminder subscription preference action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        $hash  = $this->getRequest()->getParam('hash');

        if (!$email
            || !$hash
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || !$this->unsubscribeUrl->isValidHash($email, $hash)
        ) {
            $this->messageManager->addErrorMessage(__('Your email is invalid.'));
        } else {
            try {
                $this->unsubscribedRepository->getByEmail($email);
            } catch (NoSuchEntityException $e) {
                try {
                    /** @var UnsubscribedInterface $unsubscribed */
                    $unsubscribed = $this->unsubscribedFactory->create();
                    $unsubscribed->setEmail($email);
                    $this->unsubscribedRepository->save($unsubscribed);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(
                        __('Something went wrong while saving your subscription.')
                    );

                    return $this->_redirect($this->getRedirectPath());
                }
            }

            $this->messageManager->addSuccessMessage(__('We have updated your subscription.'));
        }

        return $this->_redirect($this->getRedirectPath());
    }

    /**
     * @return string
     */
    protected function getRedirectPath()
    {
        if ($this->customerSession->isLoggedIn()) {
            return 'customer/account/';
        }

        return '';
    }
}
