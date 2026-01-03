<?php

namespace BigConnect\ProductConfigurator\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory; 

class FieldOptionPlugin
{
    protected $scopeConfig;
    protected $storeManager;
    protected $helper;
    protected $fieldsCount = 0;
    protected $currencyFactory;

    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager, \BigConnect\ProductConfigurator\Helper\Data $helper, CurrencyFactory $currencyFactory)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->currencyFactory = $currencyFactory; 

    }

    public function afterToHtml(\Magento\Catalog\Block\Product\View\Options\Type\Text $subject, $result)
    {
        $currentOption = $subject->getOption();
        $product = $currentOption->getProduct();
        $options = $product->getOptions();


        $productId = $product->getId();
        $jsonConfig = new \stdClass();
        $defaultStoreTitle = $currentOption->setStoreId(1)->getTitle();


        $customOptionTitles = $this->helper->getCustomOptionTitles($productId);
        $calculationMarkers = $this->helper->getCalculationMarkers($productId);
        $calculationMinMax  = $this->helper->getCalculationMinMax($productId);
        $dimensionOptionPrices = $this->helper->getDimensionOptionPricesNew($productId);

        $store = $this->storeManager->getStore();

        $baseCurrencyCode = $store->getBaseCurrencyCode();
        $currentCurrencyCode = $store->getCurrentCurrencyCode();

        $baseCurrency = $this->currencyFactory->create()->load($baseCurrencyCode);
        $currentCurrency = $this->currencyFactory->create()->load($currentCurrencyCode);
        $rate = $baseCurrency->getRate($currentCurrency);

        if (in_array($defaultStoreTitle, $customOptionTitles)) {
            
            $this->fieldsCount++;
            if ($this->fieldsCount == 1) {
                    $result = '<div class="bigconnect-configurator-main"><span class="price-slider-title">'.__('Size').'</span><div class="price-configurator-group">' . $result;
                }

            $custom_class = iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtolower($defaultStoreTitle, "UTF-8"));

            $optionPrice = $dimensionOptionPrices[$defaultStoreTitle] ?? '';
            $optionId = null;

            $calculationLimit = '';
            $calculationIdentificator = '';
            $min = $calculationMinMax[$defaultStoreTitle]['min'] ?? '';
            $max = $calculationMinMax[$defaultStoreTitle]['max'] ?? '';

            if (!empty($min) && !empty($max)) {
                $calculationLimit= 'data-option-' . $custom_class . '-min="' . $min . '" min=' . $min . ' max=' . $max . ' data-option-' . $custom_class . '-max="' . $max . '"';
                $calculationIdentificator = '<span class="option-measurement-text">(' . $min . '-' . $max . 'cm)</span>';
            }

            // Get price for the current option
            foreach ($options as $option) {
                if ($option->getTitle() == $defaultStoreTitle) {
                    $optionId = $option->getId();
                }
            }

            // Remove the price HTML
            $pricePattern = '/<span class="price-notice">.+?<\/span>/s';
            $result = preg_replace($pricePattern, "", $result);

            // Remove the note HTML
            $notePattern = '/<p class="note note_[^"]+">.*?<\/p>/s';
            $result = preg_replace($notePattern, "", $result);

            // Add a custom class to the parent div
            $result = str_replace('<div class="field required">', '<div class="field required range-slider slider-'.$custom_class.' range-slider-id-'.$optionId.'" data-option-rate="'.$rate.'" data-role="configurator" data-option-price="'.$optionPrice.'" '.$calculationLimit.'>', $result);

          

            

            
            


            // Add calculation marker to the title
            if (isset($calculationMarkers[$defaultStoreTitle])) {
                $marker = $calculationMarkers[$defaultStoreTitle];
                $result = preg_replace('/(<label class="label".*?>)(.*?)(<\/label>)/s', '$2'.'<b>'.$marker.'</b> '.$calculationIdentificator.'$1$3', $result);
            }

            if ($this->fieldsCount == count($customOptionTitles)) {
                    $result = $result . '</div></div>';
                }

        } 

        

        return $result;
    }

}
