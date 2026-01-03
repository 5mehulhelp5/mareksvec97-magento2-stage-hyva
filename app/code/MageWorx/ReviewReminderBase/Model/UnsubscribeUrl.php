<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model;

use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class UnsubscribeUrl
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * UnsubscribeUrl constructor.
     *
     * @param UrlFactory $urlFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlFactory $urlFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->urlFactory   = $urlFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $storeId
     * @param string $email
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUnsubscribeUrl(int $storeId, string $email): string
    {
        $store    = $this->storeManager->getStore($storeId);
        $isSecure = $store->isUrlSecure();

        $url = rtrim(
                $store->getBaseUrl(
                    UrlInterface::URL_TYPE_LINK,
                    $isSecure
                ),
                '/'
            ) . '/' .
            ltrim('reviewreminder/manage/unsubscribe', '/');

        $params = [];

        if (!$store->isUseStoreInUrl()) {
            $params['___store'] = $store->getCode();
        }

        $params = array_merge(
            $params,
            [
                'email' => $email,
                'hash'  => $this->getEmailHash($email)
            ]
        );
        $query  = $params ? '?' . http_build_query($params) : '';

        return $url . $query;
    }

    /**
     * @param string $email
     * @param string $hash
     * @return bool
     */
    public function isValidHash(string $email, string $hash): bool
    {
        return $hash === $this->getEmailHash($email);
    }

    /**
     * @param string $email
     * @return string
     */
    private function getEmailHash(string $email): string
    {
        return hash("sha256", 'xrow_' . $email . '_egam');
    }
}
