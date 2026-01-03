<?php

namespace Zymion\SeoFriendlyImages\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Save the filename overwrite after saving a product.
 */
class ProductSaveAfter implements ObserverInterface
{

    /**
     * Request interface
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Resource Connection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->request = $request;
        $this->resource = $resource;
    }

    /**
     * Save filename overwrite in database
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->request->getPostValue();

        if (isset($data['product']['media_gallery']['images'])) {
            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('catalog_product_entity_media_gallery_value');
            $product = $observer->getProduct();
            $mediaGallery = $product->getMediaGallery();

            if (isset($mediaGallery['images'])) {
                foreach ($mediaGallery['images'] as $image) {
                    if (array_key_exists('filename_overwrite', $image)) {
                        $sql = "UPDATE " . $tableName
                            . " SET filename_overwrite = '" . $image['filename_overwrite']
                            . "' WHERE value_id = " . $image['value_id'];
                        $connection->query($sql);
                    }
                }
            }
        }
    }
}
