<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class HomepageCategories implements ArgumentInterface
{
    private const XML_PATH_PREFIX = 'hyvastarter/homepage_categories/';

    private ScopeConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;
    private CategoryRepositoryInterface $categoryRepository;
    private CategoryHelper $categoryHelper;
    private UrlInterface $urlBuilder;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        CategoryHelper $categoryHelper,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryHelper = $categoryHelper;
        $this->urlBuilder = $urlBuilder;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getTitle(): string
    {
        $value = $this->getValue('title');
        return $value !== '' ? $value : 'Vybrané kategórie';
    }

    public function getSubtitle(): string
    {
        $value = $this->getValue('subtitle');
        return $value !== '' ? $value : 'Objavte našu ponuku…';
    }

    public function getItems(): array
    {
        $ids = $this->getCategoryIds();
        if (!$ids) {
            return [];
        }

        $limit = (int) $this->getValue('limit');
        if ($limit > 0) {
            $ids = array_slice($ids, 0, $limit);
        }

        $showDesc = $this->scopeConfig->isSetFlag(
            self::XML_PATH_PREFIX . 'show_desc',
            ScopeInterface::SCOPE_STORE
        );
        $descMaxLen = (int) $this->getValue('desc_maxlen');

        $storeId = (int) $this->storeManager->getStore()->getId();
        $mediaBase = rtrim($this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]), '/');
        $fallbackImage = $this->getFallbackImageUrl($mediaBase);

        $items = [];
        foreach ($ids as $id) {
            try {
                $category = $this->categoryRepository->get($id, $storeId);
            } catch (\Throwable $e) {
                continue;
            }

            if (!$category->getIsActive()) {
                continue;
            }

            $image = (string) ($category->getData('thumbnail') ?: $category->getImage());
            $imageUrl = $this->buildCategoryImageUrl($image, $mediaBase);
            if ($imageUrl === '' && $fallbackImage !== '') {
                $imageUrl = $fallbackImage;
            }

            $items[] = [
                'id' => $id,
                'name' => (string) $category->getName(),
                'url' => $this->categoryHelper->getCategoryUrl($category),
                'img' => $imageUrl,
                'desc' => $showDesc
                    ? $this->excerpt((string) $category->getDescription(), $descMaxLen)
                    : '',
            ];
        }

        return $items;
    }

    private function getValue(string $field): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PREFIX . $field,
            ScopeInterface::SCOPE_STORE
        );
    }

    private function getCategoryIds(): array
    {
        $rawIds = $this->getValue('category_ids');
        if ($rawIds === '') {
            return [];
        }

        $parts = preg_split('/[,\s]+/', trim($rawIds), -1, PREG_SPLIT_NO_EMPTY);
        $ids = [];
        foreach ($parts as $part) {
            if (ctype_digit($part)) {
                $ids[] = (int) $part;
            }
        }

        return array_values(array_unique($ids));
    }

    private function buildCategoryImageUrl(string $file, string $mediaBase): string
    {
        $file = trim($file);
        if ($file === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $file)) {
            return $file;
        }

        $relative = ltrim($file, '/');
        if (stripos($relative, 'media/') === 0) {
            $relative = substr($relative, 6);
        }
        if (stripos($relative, 'catalog/category/') !== 0) {
            $relative = 'catalog/category/' . $relative;
        }

        return $mediaBase . '/' . $relative;
    }

    private function getFallbackImageUrl(string $mediaBase): string
    {
        $value = ltrim($this->getValue('fallback_image'), '/');
        if ($value === '') {
            return '';
        }

        return $mediaBase . '/hyvastarter/homepage_categories/' . $value;
    }

    private function excerpt(string $html, int $limit): string
    {
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($limit <= 0 || mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit, 'UTF-8');
        $pos = mb_strrpos($cut, ' ', 0, 'UTF-8');
        if ($pos !== false) {
            $cut = mb_substr($cut, 0, $pos, 'UTF-8');
        }

        return rtrim($cut, ',.;:!?') . '…';
    }
}
