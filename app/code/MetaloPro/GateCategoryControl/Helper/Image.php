<?php
namespace MetaloPro\GateCategoryControl\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Image extends AbstractHelper
{
    protected StoreManagerInterface $storeManager;
    protected AssetRepository $assetRepo;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        AssetRepository $assetRepo
    ) {
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        parent::__construct($context);
    }

    /**
     * Vráti URL kategórie, alebo placeholder, ak nie je obrázok zadaný
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getImage($category): string
    {
        $image = $category->getImage(); // natívny atribút kategórie

        if ($image) {
            return $this->getImageUrl($image);
        }

        // fallback na default placeholder
        return $this->assetRepo->getUrl('Magento_Catalog::images/product/placeholder/small_image.jpg');
    }

    /**
     * Vráti absolútnu URL k obrázku kategórie
     *
     * @param string $image
     * @return string
     */
    public function getImageUrl(string $image): string
    {
        // Ak je $image už kompletná URL, rovno ju vráť
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        // Odstráň zdvojenia
        $image = ltrim($image, '/');
        $image = preg_replace('#^media/#', '', $image);

        $mediaBaseUrl = rtrim($this->storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA), '/');

        return $mediaBaseUrl . '/' . $image;
    }
}
