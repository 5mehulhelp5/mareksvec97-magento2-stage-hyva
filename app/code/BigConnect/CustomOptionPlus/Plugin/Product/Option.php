<?php

namespace BigConnect\CustomOptionPlus\Plugin\Product;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Option
{
    protected $scopeConfig;
    protected $storeManager;

    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function afterGetValuesHtml(\Magento\Catalog\Block\Product\View\Options\Type\Select $subject, $result)
    {
        $currentOption = $subject->getOption();
        $defaultStoreOptionValues = $currentOption->setStoreId(1)->getValues();
        $colorGroups = $this->getColorGroups();
        $currentStoreCode = $this->storeManager->getStore()->getCode();

        

        foreach ($colorGroups as $colorGroup) {
            $searchText = $this->scopeConfig->getValue('customoptionplus/'.$colorGroup.'/search_text', ScopeInterface::SCOPE_STORE);
            $infText = $this->scopeConfig->getValue('customoptionplus/'.$colorGroup.'/ral_text', ScopeInterface::SCOPE_STORE);
            $imageUrl = '/customoptionplus/' . $this->scopeConfig->getValue('customoptionplus/'.$colorGroup.'/image_url_text', ScopeInterface::SCOPE_STORE);
            $optionTitleRektification = $this->scopeConfig->getValue('customoptionplus/title_group/rektifikacia_title', ScopeInterface::SCOPE_STORE);
            $optionTitleColor = $this->scopeConfig->getValue('customoptionplus/title_group/color_title', ScopeInterface::SCOPE_STORE);

            // Add the base media URL to the image URL
            $imageUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $imageUrl;

            foreach ($defaultStoreOptionValues as $value) {
                if ($value->getTitle() == $searchText) {

                    $defaultStoreTitle = $currentOption->setStoreId(1)->getTitle();
                    $titleText = $searchText;
                        
                    if (($defaultStoreTitle == $optionTitleRektification) || ($defaultStoreTitle == "Rektifikácia")) {
                    // Create the HTML for the image, text, and RAL color
                    $imageHtml = '<img src="'.$imageUrl.'" alt="'.$titleText.'">';
                    $textHtml = '<div class="color-name">'.$titleText.'</div>';
                    $ralHtml = $infText ? '<div class="ral-color">'.$infText.'</div>' : ''; // Add RAL color if available

                    // Create the modified option HTML
                    $optionHtml = $imageHtml .'<div class="group-rect">'. $textHtml . $ralHtml .'</div>';

                    // Replace the original option HTML with the modified one in the result
                    $pattern = '/<span>\s*'.preg_quote($value->getTitle(), '/').'\s*<\/span>/';
                    $result = preg_replace($pattern, $optionHtml, $result);
                    }

                    if (($defaultStoreTitle == $optionTitleColor) || ($defaultStoreTitle == "Farba")) {
                    // Create the HTML for the image, text, and RAL color
                    $imageHtml = '<img src="'.$imageUrl.'" alt="'.$titleText.'">';
                    $textHtml = '<div class="color-name">'.$titleText.'</div>';
                    $ralHtml = $infText ? '<div class="ral-color">'.$infText.'</div>' : ''; // Add RAL color if available

                    // Create the modified option HTML
                    $optionHtml = $imageHtml . $textHtml . $ralHtml;

                    // Replace the original option HTML with the modified one in the result
                    $pattern = '/<span>\s*'.preg_quote($value->getTitle(), '/').'\s*<\/span>/';
                    $result = preg_replace($pattern, $optionHtml, $result);    
                    }

                }
            }
        }

        // Add custom class to options-list div if the default store title is "Farba"
        $defaultStoreTitle = $currentOption->setStoreId(1)->getTitle();
        if (($defaultStoreTitle == $optionTitleColor) || ($defaultStoreTitle == "Farba")) {
            $result = str_replace('<div class="options-list nested"', '<div class="options-list nested color_options"', $result);
            
        }
        elseif (($defaultStoreTitle != $optionTitleRektification) || ($defaultStoreTitle != "Rektifikácia")) {
            $result = str_replace('<div class="options-list nested"', '<div class="options-list nested classic-options"', $result);
        }

        if (($defaultStoreTitle == $optionTitleRektification) || ($defaultStoreTitle == "Rektifikácia")) {
            $result = str_replace('<div class="options-list nested"', '<div class="options-list nested rectification_options"', $result);
        }


        return $result;
    }

    private function getColorGroups()
    {
        // This should be replaced by logic to get color groups from database.
        return ['black_color', 'white_color','antracit_color','metalic_bronze_color','brown_color','gold_color','steel_color','other_color','no_rectification','with_rectification','other_color','with_hidden_rectification'];
    }


}
