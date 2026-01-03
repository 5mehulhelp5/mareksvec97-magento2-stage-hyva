<?php
namespace BigConnect\ProductConfigurator\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Option\Repository as OptionRepository;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class Data
{
    protected $productRepository;
    protected $optionRepository;
    protected $appEmulation;
    protected $storeManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        OptionRepository $optionRepository,
        Emulation $appEmulation,
        StoreManagerInterface $storeManager
    )
    {
        $this->productRepository = $productRepository;
        $this->appEmulation = $appEmulation;
        $this->optionRepository = $optionRepository;
        $this->storeManager = $storeManager;
    }

    protected $dimensions = [
        'length' => 'Dĺžka',
        'width' => 'Šírka',
        'height' => 'Výška',
    ];

    // Metoda získává názvy vlastních možností pro produkt s daným ID
    public function getCustomOptionTitles($productId)
    {
        $product = $this->productRepository->getById($productId);
        $optionTitles = [];

        foreach ($this->dimensions as $dimension => $title) {
            if ($product->getCustomAttribute('calculation_'.$dimension.'_enable') && $product->getCustomAttribute('calculation_'.$dimension.'_enable')->getValue() == '1') {
                $optionTitles[] = $title;
            }
        }

        return $optionTitles;
    }

    // Metoda získává markery výpočtu pro produkt s daným ID
    public function getCalculationMarkers($productId)
    {
        $product = $this->productRepository->getById($productId);
        $markers = [];

        foreach ($this->dimensions as $dimension => $title) {
            if ($product->getCustomAttribute('calculation_'.$dimension.'_enable') && $product->getCustomAttribute('calculation_'.$dimension.'_enable')->getValue() == '1') {
                $marker = $product->getCustomAttribute('calculation_'.$dimension.'_marker') ? $product->getCustomAttribute('calculation_'.$dimension.'_marker')->getValue() : '';
                $markers[$title] = $marker;
            }
        }

        return $markers;
    }


    // Metoda získává minimální a maximální hodnoty pro každou dimenzi produktu s daným ID
    public function getCalculationMinMax($productId)
    {
        $product = $this->productRepository->getById($productId);
        $minMax = [];

        foreach ($this->dimensions as $dimension => $title) {
            if ($product->getCustomAttribute('calculation_'.$dimension.'_enable') && $product->getCustomAttribute('calculation_'.$dimension.'_enable')->getValue() == '1') {
                $min = $product->getCustomAttribute('calculation_'.$dimension.'_min') ? $product->getCustomAttribute('calculation_'.$dimension.'_min')->getValue() : '';
                $max = $product->getCustomAttribute('calculation_'.$dimension.'_max') ? $product->getCustomAttribute('calculation_'.$dimension.'_max')->getValue() : '';
                $minMax[$title]['min'] = $min;
                $minMax[$title]['max'] = $max;
            }
        }

        return $minMax;
    }

    

    public function getDimensionOptionPricesNew($productId)
        {
            $product = $this->productRepository->getById($productId);
            $optionPrices = [];

            foreach ($this->dimensions as $dimension => $title) {
                if ($product->getCustomAttribute('calculation_'.$dimension.'_enable') && $product->getCustomAttribute('calculation_'.$dimension.'_enable')->getValue() == '1') {
                    $price = $product->getCustomAttribute($dimension.'_unit_price') ? $product->getCustomAttribute($dimension.'_unit_price')->getValue() : '';
                    $optionPrices[$title] = $price;
                }
            }

            return $optionPrices;
        }

    
}
