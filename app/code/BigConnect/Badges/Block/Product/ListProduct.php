<?php

namespace BigConnect\Badges\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Url\Helper\Data;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
	
    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Retrieve product discount percent
     *
     * @param Product $product
     * @return string|bool
     */
    public function getDiscountPercent(Product $product) {
        try {
            $priceCode = \Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE;
            $finalPriceCode = \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE;
            $price = $product->getPriceInfo()->getPrice($priceCode)->getAmount()->getValue();
            $finalPrice = $product->getPriceInfo()->getPrice($finalPriceCode)->getAmount()->getValue();
            
            if (!$finalPrice || !$price) {
                return false;
            }

            $discountPercent = 100 - round($finalPrice / $price * 100, 2, PHP_ROUND_HALF_DOWN);

            if ($finalPrice < $price && $discountPercent < 100) {
                return sprintf('%d%%', $discountPercent);
            }
        } catch (\Exception $exception) {
            return false;
        }

        return false;
    }

    /**
     * Bestseller Badge
     *
     */

    public function isProductBestseller(Product $product)
    {
       $getBoolean = $product->getData('bestseller');
       $betsellerText = "Bestseller";
       if($getBoolean == 1) {
            return $betsellerText;
        }
        else {
            return false;
        }
       
    }

    public function isInStock(Product $product)
    {
       $getStock = $product->getData('dostupnost');
       $stockText = __('In stock'); 
       if($getStock == 5) {
            return $stockText;
        }
        else {
            return false;
        }
       
    }

    public function isPowderColors(Product $product)
    {
       $powderColor = $product->getData('powder_colors');
       $powderColorText = __('Powder colors'); 
       if($powderColor == 1) {
            return $powderColorText;
        }
        else {
            return false;
        }
       
    }

   public function getWarrantyBadge(Product $product)
   {
       $warrantyValue = $product->getData('zaruka');

       if (!$warrantyValue) {
           return false;
       }

       $attribute = $product->getResource()->getAttribute('zaruka');
       if ($attribute && $attribute->usesSource()) {
           $label = $attribute->getSource()->getOptionText($warrantyValue);

           if ($label) {
               return '
                   <div class="single-badge">
                       <div class="warranty-badge flex items-center bg-gray-700 text-white text-xs font-medium px-2 py-1 rounded">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="text-white mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l8 4v5c0 5.25-3.44 9.74-8 11-4.56-1.26-8-5.75-8-11V7l8-4z" />
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" />
                           </svg>
                           <span>' . htmlspecialchars($label) . '</span>
                       </div>
                   </div>';
           }
       }

       return false;
   }


    public function getPersonalizeBadge(Product $product)
    {
        $getLength = $product->getData('calculation_length_enable');
        $getHeight = $product->getData('calculation_height_enable');
        $getWidth  = $product->getData('calculation_width_enable');

        if ($getLength == 1 || $getHeight == 1 || $getWidth == 1) {
            return 
                '<div class="single-badge">
                <div class="personalized-product">
                <span class="icon-per">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_9455_5792)">
                    <path d="M10.1751 2.94983C9.99202 2.76672 9.69541 2.76672 9.5123 2.94983L0.137334 12.3248C-0.045778 12.5079 -0.045778 12.8045 0.137334 12.9876L2.01233 14.8626C2.10387 14.9542 2.22382 15 2.34377 15C2.46371 15 2.58363 14.9542 2.6752 14.8626L12.0501 5.48767C12.2332 5.30455 12.2332 5.00795 12.0501 4.82483L10.1751 2.94983ZM2.34373 13.8684L1.1316 12.6562L7.49998 6.28786L8.71214 7.50002C8.71214 7.49999 2.34373 13.8684 2.34373 13.8684ZM9.37498 6.83716L8.16281 5.62499L9.8437 3.9441L11.0559 5.15627L9.37498 6.83716Z" fill="white"/>
                    <path d="M6.5625 3.75C7.03126 2.81252 7.49998 2.34376 8.4375 1.875C7.50002 1.40624 7.03126 0.937515 6.5625 0C6.09374 0.937485 5.62495 1.40624 4.6875 1.875C5.62502 2.34376 6.09374 2.81252 6.5625 3.75Z" fill="white"/>
                    <path d="M14.0625 6.5625C13.8281 7.03126 13.5937 7.26562 13.125 7.49998C13.5938 7.73435 13.8281 7.96874 14.0625 8.43747C14.2968 7.96871 14.5312 7.73435 15 7.49998C14.5312 7.26562 14.2969 7.03123 14.0625 6.5625Z" fill="white"/>
                    <path d="M14.0625 1.40624C13.3594 1.05467 13.0078 0.703121 12.6562 0C12.3047 0.703121 11.9531 1.0547 11.25 1.40624C11.9531 1.75782 12.3047 2.10936 12.6562 2.81248C13.0078 2.10936 13.3594 1.75779 14.0625 1.40624Z" fill="white"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_9455_5792">
                    <rect width="15" height="15" fill="white"/>
                    </clipPath>
                    </defs>
                    </svg>

                    </span>


                    <span class="per-text">' . __("The possibility of customizing the product") . '</span>
                    </div>
                </div>';
            
        } else {
            return false;
        }
    }




    

   
	


}