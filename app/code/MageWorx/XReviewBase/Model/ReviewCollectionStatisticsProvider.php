<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

class ReviewCollectionStatisticsProvider extends \Magento\Framework\DataObject
{
    const CUSTOMER_COUNT = 'customer_count';
    const LOCATION_COUNT = 'location_count';
    const MEDIA_COUNT    = 'media_review_count';

    /**
     * @var \MageWorx\GeoIP\Model\Geoip
     */
    protected $geoIp;

    /**
     * ReviewCollectionStatisticsProvider constructor.
     *
     * @param \MageWorx\GeoIP\Model\Geoip $geoIp
     */
    public function __construct(
        \MageWorx\GeoIP\Model\Geoip $geoIp
    ) {
        $this->geoIp = $geoIp;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $collection
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    public function composeStatistics($collection)
    {
        if (!$this->out($collection)) {
            $select = clone $collection->getSelect();
            $select->reset(\Magento\Framework\DB\Select::ORDER);
            $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
            $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
            $select->reset(\Magento\Framework\DB\Select::COLUMNS);
            $select->limit(null);

            $distinct = ($select->getPart(\Magento\Framework\DB\Select::GROUP)) ? 'DISTINCT' : '';

            $columns = [];

            if ($collection->getFlag('mageworx_need_customer_count')) {
                $columns[self::CUSTOMER_COUNT] = new \Zend_Db_Expr(
                    "COUNT(" . $distinct . " IF(detail.is_verified = '1', 1, NULL))"
                );
                $collection->setFlag('mageworx_need_customer_count', null);
            }

            if ($collection->getFlag('mageworx_need_location_count') && $this->getCountryCode()) {
                $columns[self::LOCATION_COUNT] = new \Zend_Db_Expr(
                    "COUNT(" . $distinct . " IF(detail.location = '" . $this->getCountryCode() . "', 1, NULL))"
                );
                $collection->setFlag('mageworx_need_location_count', null);
            }

            if ($collection->getFlag('mageworx_need_media_count')) {
                $columns[self::MEDIA_COUNT] = new \Zend_Db_Expr(
                    "COUNT(" . $distinct . " media_table.review_id)"
                );
                $collection->setFlag('mageworx_need_media_count', null);
            }

            $select->columns($columns);

            $rawResult = $collection->getConnection()->fetchAll($select);

            $this->addData(
                [
                    self::CUSTOMER_COUNT => array_sum(array_column($rawResult, self::CUSTOMER_COUNT)),
                    self::LOCATION_COUNT => array_sum(array_column($rawResult, self::LOCATION_COUNT)),
                    self::MEDIA_COUNT    => array_sum(array_column($rawResult, self::MEDIA_COUNT)),
                ]
            );
        }
    }

    /**
     * @return mixed
     */
    public function getCustomerCount(): int
    {
        return (int)$this->getData(self::CUSTOMER_COUNT);
    }

    /**
     * @return int
     */
    public function getMediaCount(): int
    {
        return (int)$this->getData(self::MEDIA_COUNT);
    }

    /**
     * @return int
     */
    public function getLocationReviewCount(): int
    {
        return (int)$this->getData(self::LOCATION_COUNT);
    }

    /**
     * @return string|null
     */
    protected function getCountryCode(): ?string
    {
        $data = $this->geoIp->getCurrentLocation();

        return $data['code'] ?? null;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $collection
     * @return bool
     */
    protected function out($collection): bool
    {
        if ($collection->getFlag('mageworx_need_customer_count')
            || ($collection->getFlag('mageworx_need_location_count') && $this->getCountryCode())
            || $collection->getFlag('mageworx_need_media_count')
        ) {
            return false;
        }

        return true;
    }
}
