<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content;

use Magento\Framework\DataObject;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\Component\Product as ReminderProduct;

abstract class DataContainer extends DataObject implements DataContainerInterface
{
    const CUSTOMER_ID = 'customer_id';

    const CUSTOMER_FIRSTNAME = 'customer_firstname';

    const CUSTOMER_NAME = 'customer_name';

    const CUSTOMER_EMAIL = 'customer_email';

    const ORDER_PRODUCTS = 'order_products';

    const STORE_ID = 'store_id';

    const EMAIL_TEMPLATE_ID = 'email_template_id';

    /**
     * @param int $id
     * @return DataContainer
     */
    public function setCustomerId(int $id): DataContainer
    {
        return $this->setData(self::CUSTOMER_ID, $id);
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID) ? (int)$this->getData(self::CUSTOMER_ID) : null;
    }

    /**
     * @param string $name
     * @return DataContainer
     */
    public function setCustomerName(?string $name): DataContainer
    {
        return $this->setData(self::CUSTOMER_NAME, $name);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getCustomerName();
    }

    /**
     * @return string|null
     */
    public function getCustomerName(): ?string
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @param string $name
     * @return DataContainer
     */
    public function setCustomerFirstName(?string $name): DataContainer
    {
        return $this->setData(self::CUSTOMER_FIRSTNAME, $name);
    }

    /**
     * @return string|null
     */
    public function getCustomerFirstName(): ?string
    {
        return $this->getData(self::CUSTOMER_FIRSTNAME);
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->getCustomerFirstName();
    }

    /**
     * @param string $email
     * @return DataContainer
     */
    public function setCustomerEmail(string $email): DataContainer
    {
        return $this->setData(self::CUSTOMER_EMAIL, $email);
    }

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     *
     * Array structure:
     *
     * [
     *     'order_id' => [
     *          'product_id_1' => [
     *              'product_field_1' => 'product_param_1'
     *              'product_field_n' => 'product_param_n'
     *          ],
     *          ...
     *          'product_id_N' => [
     *              'product_field_1' => 'product_param_1'
     *              'product_field_n' => 'product_param_n'
     *          ]
     *      ]
     * ]
     *
     * @return array|null
     */
    public function getOrderProducts(): ?array
    {
        return $this->getData(self::ORDER_PRODUCTS);
    }

    /**
     * @return array
     */
    public function getProductIds(): array
    {
        $ids = [];

        foreach ($this->getOrderProducts() as $orderProducts) {
            $ids = array_merge($ids, array_keys($orderProducts));
        }

        return $ids;
    }

    /**
     * @param int $orderId
     * @param array $products
     * @return array
     */
    public function addOrderProducts(int $orderId, array $products): ?array
    {
        $info           = $this->getData(self::ORDER_PRODUCTS);
        $info[$orderId] = $products;
        $this->setData(self::ORDER_PRODUCTS, $info);

        return $info;
    }

    /**
     * @param array $productIds
     * @param bool $isValidProductIds
     * @return bool
     */
    public function removeProducts(array $productIds, bool $isValidProductIds = false): bool
    {
        $info   = $this->getData(self::ORDER_PRODUCTS);
        $result = false;

        foreach ($info as $orderId => $products) {
            foreach ($products as $productId => $productData) {
                if (($isValidProductIds && !in_array($productId, $productIds))
                    || (!$isValidProductIds && in_array($productId, $productIds))
                ) {
                    unset($info[$orderId][$productId]);
                    $result = true;
                }
            }
        }

        if ($result) {
            $this->setData(self::ORDER_PRODUCTS, $info);
        }

        return $result;
    }

    /**
     * @param array $productIds
     * @param bool $isValidProductIds
     * @return bool
     */
    public function removeDuplicateProducts()
    {
        $info   = $this->getData(self::ORDER_PRODUCTS);
        $productIds = [];
        $result = false;

        foreach ($info as $orderId => $products) {
            foreach ($products as $productId => $productData) {
                if (in_array($productId, $productIds)) {
                    unset($info[$orderId][$productId]);
                    $result = true;
                } else {
                    $productIds[] = $productId;
                }
            }
        }

        if ($result) {
            $this->setData(self::ORDER_PRODUCTS, $info);
        }
    }

    /**
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @param int|null $storeId
     * @return DataContainer
     */
    public function setStoreId(int $storeId): DataContainer
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $result = $this->getData();

        foreach ($this->getOrderProducts() as $orderId => $orderData) {

            /** @var ReminderProduct $product */
            foreach ($orderData as $productId => $product) {
                $result[self::ORDER_PRODUCTS][$orderId][$productId] = $product->getData();
            }
        }

        return $result;
    }
}
