<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Observer;

use Exception;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use MageWorx\ReviewReminderBase\Model\Cookie\Email as EmailCookie;

class SetCookieObserver implements ObserverInterface
{
    /**
     * @var EmailCookie
     */
    protected $cookie;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * SetCookieObserver constructor.
     *
     * @param EmailCookie $cookie
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EmailCookie $cookie,
        EncryptorInterface $encryptor
    ) {
        $this->cookie    = $cookie;
        $this->encryptor = $encryptor;
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if ($order) {
            $email = $order->getCustomerEmail();
            if ($email) {
                try {
                    $this->cookie->set($this->encryptor->encrypt($email));
                } catch (Exception $e) {

                }
            }
        }
    }
}
