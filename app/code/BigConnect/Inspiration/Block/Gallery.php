<?php
namespace BigConnect\Inspiration\Block;

use BigConnect\Inspiration\Model\Inspiration;
use BigConnect\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class Gallery extends Template
{
    private CollectionFactory $collectionFactory;
    private StoreManagerInterface $storeManager;
    private Registry $registry;
    private ProductRepositoryInterface $productRepository;
    private ImageHelper $imageHelper;
    private PriceHelper $priceHelper;
    private ConfigurableType $configurableType;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        PriceHelper $priceHelper,
        ConfigurableType $configurableType,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->configurableType = $configurableType;
    }

    public function getGalleryItems(): array
    {
        $context = (string)$this->getData('context');
        $limit = (int)$this->getData('limit');
        $storeId = (int)$this->storeManager->getStore()->getId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', Inspiration::STATUS_APPROVED);
        $collection->addFieldToFilter('store_id', ['in' => [0, $storeId]]);

        if ($context === 'product') {
            $productIds = $this->getCurrentProductIds();
            if (!$productIds) {
                return [];
            }
            $collection->addFieldToFilter('product_id', ['in' => $productIds]);
        }

        $collection->setOrder('position', 'ASC');
        $collection->setOrder('created_at', 'DESC');

        if ($limit) {
            $collection->setPageSize($limit);
        }

        $items = [];
        foreach ($collection as $item) {
            $productData = $this->getProductData((int)$item->getData('product_id'));
            $items[] = [
                'id' => (int)$item->getId(),
                'image_url' => $this->getMediaUrl((string)$item->getData('image')),
                'customer_name' => (string)$item->getData('customer_name'),
                'location' => (string)($item->getData('location') ?? ''),
                'country_code' => (string)($item->getData('country_code') ?? ''),
                'country_flag' => $this->getFlagUrl((string)($item->getData('country_code') ?? '')),
                'rating' => (int)$item->getData('rating'),
                'review' => (string)($item->getData('review') ?? ''),
                'review_excerpt' => $this->getExcerpt((string)($item->getData('review') ?? ''), 140),
                'created_at' => $this->formatDate((string)$item->getData('created_at')), 
                'product_id' => (int)$item->getData('product_id'),
                'product_name' => $productData['name'],
                'product_url' => $productData['url'],
                'product_image' => $productData['image'],
                'product_price' => $productData['price'],
            ];
        }

        return $items;
    }

    public function getItemsJson(): string
    {
        return (string)json_encode(
            $this->getGalleryItems(),
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
        );
    }

    public function hasItems(): bool
    {
        return (bool)$this->getGalleryItems();
    }

    public function shouldShowHeader(): bool
    {
        return (bool)$this->getData('show_header');
    }

    public function getSectionTitle(): string
    {
        return (string)$this->getData('title');
    }

    private function getCurrentProductIds(): array
    {
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return [];
        }

        $productId = (int)$product->getId();
        $parentIds = $this->configurableType->getParentIdsByChild($productId);
        $ids = array_merge([$productId], $parentIds);

        return array_values(array_unique(array_filter($ids)));
    }

    private function getProductData(int $productId): array
    {
        if (!$productId) {
            return [
                'name' => '',
                'url' => '',
                'image' => '',
                'price' => '',
            ];
        }

        try {
            $product = $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());
        } catch (\Exception $exception) {
            return [
                'name' => '',
                'url' => '',
                'image' => '',
                'price' => '',
            ];
        }

        $imageUrl = $this->imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getSmallImage())
            ->getUrl();

        return [
            'name' => (string)$product->getName(),
            'url' => (string)$product->getProductUrl(),
            'image' => $imageUrl,
            'price' => $this->priceHelper->currency($product->getFinalPrice(), true, false),
        ];
    }

    private function getMediaUrl(string $path): string
    {
        if ($path === '') {
            return '';
        }

        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . ltrim($path, '/');
    }

    private function getFlagUrl(string $countryCode): string
    {
        if ($countryCode === '') {
            return '';
        }

        $file = sprintf('images/flags/%s.svg', strtolower($countryCode));
        return $this->getViewFileUrl($file);
    }

    private function getExcerpt(string $text, int $length): string
    {
        $clean = trim(strip_tags($text));
        if (mb_strlen($clean) <= $length) {
            return $clean;
        }

        return rtrim(mb_substr($clean, 0, $length)) . '…';
    }
}
