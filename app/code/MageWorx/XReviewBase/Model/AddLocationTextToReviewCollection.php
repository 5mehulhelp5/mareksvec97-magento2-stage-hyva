<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\XReviewBase\Model;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class AddLocationTextToReviewCollection
{
    /**
     * @var CountryCollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * @var LocationTextCreator
     */
    protected $locationTextCreator;

    /**
     * AddLocationTextToReviewCollection constructor.
     *
     * @param CountryCollectionFactory $countryCollectionFactory
     */
    public function __construct(
        CountryCollectionFactory $countryCollectionFactory,
        LocationTextCreator $locationTextCreator
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->locationTextCreator = $locationTextCreator;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $reviewCollection
     * @return void
     */
    public function execute($reviewCollection)
    {
        if (!$reviewCollection->getFlag('mageworx_location_text_added')) {
            $entityIds = $reviewCollection->getColumnValues('review_id');

            if (!$entityIds) {
                return $this;
            }

            $countryIds = array_unique($reviewCollection->getColumnValues('location'));

            if (!$countryIds) {
                return $this;
            }

            /** @var \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection */
            $countryCollection = $this->countryCollectionFactory->create();
            $countryCollection->addCountryIdFilter($countryIds);
            $countryArray = $this->toArray($countryCollection->toOptionArray());

            if (!$countryArray) {
                return $this;
            }

            foreach ($countryIds as $countryId) {
                $countryLabel = $countryArray[$countryId] ?? null;

                if ($countryLabel) {
                    foreach ($reviewCollection->getItemsByColumnValue('location', $countryId) as $review) {
                        $review->setLocationText(
                            $this->locationTextCreator->createText($countryId, $countryLabel, $review->getRegion())
                        );
                    }
                }
            }

            $reviewCollection->setFlag('mageworx_location_text_added', true);
        }
    }

    /**
     * @param array $optionArray
     * @return array
     */
    protected function toArray(array $optionArray): array
    {
        $result = [];

        foreach ($optionArray as $data) {
            $result[$data['value']] = $data['label'];
        }

        return array_filter($result);
    }
}
