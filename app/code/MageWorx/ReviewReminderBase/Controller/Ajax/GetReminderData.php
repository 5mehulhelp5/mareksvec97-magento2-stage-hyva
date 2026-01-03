<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Controller\Ajax;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Encryption\EncryptorInterface;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\PopupDataContainer;
use MageWorx\ReviewReminderBase\Model\Content\DataProvider\PopupDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class GetReminderData extends Action
{
    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PopupDataProvider
     */
    protected $popupDataProvider;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * GetReminderData constructor.
     *
     * @param Context $context
     * @param ResultJsonFactory $resultJsonFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param LoggerInterface $logger
     * @param PopupDataProvider $popupDataProvider
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        ResultJsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        LoggerInterface $logger,
        PopupDataProvider $popupDataProvider,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager      = $storeManager;
        $this->customerSession   = $customerSession;
        $this->logger            = $logger;
        $this->popupDataProvider = $popupDataProvider;
        $this->encryptor         = $encryptor;
    }

    /**
     * @return ResultJson|null
     */
    public function execute(): ?ResultJson
    {
        $microtime = microtime(true);

        if ($this->getRequest()->isAjax()) {
            $result = $this->resultJsonFactory->create();
            $email  = $this->getRequest()->getParam('email');

            if ($email !== null) {
                $email = $this->encryptor->decrypt($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->customerSession->setCustomerPreviousEmail($email);
                }
            }

            try {
                $reminderData = $this->popupDataProvider->getRemindersData();

                if (!$reminderData) {
                    throw new NoSuchEntityException(__('Requested Data doesn\'t exist'));
                }

                /** @var PopupDataContainer $emailDataContainer */
                $emailDataContainer = array_shift($reminderData);

                $emailDataContainer->setSuccess('true');

                $arrayResult = [
                    'success' => true,
                    'content' => $emailDataContainer->getConvertedContent(),
                ];

            } catch (NoSuchEntityException $e) {
                $arrayResult = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            } catch (Exception $e) {
                $this->logger->critical($e);

                $arrayResult = [
                    'message' => __('There was an error loading review reminder data.')
                ];
                $result->setHttpResponseCode(500);
            }

            //$arrayResult['time'] = number_format((microtime(true) - $microtime), 5);

            return $result->setData($arrayResult);
        }

        return null;
    }
}
