<?php

namespace BigConnect\ProductAccessory\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Url\Helper\Data;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;

class ProductAccessory extends \Magento\Catalog\Block\Product\ListProduct
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


    /**
     * Bestseller Badge
     *
     */

    public function getImbusakQty(Product $product)
    {
        $imbusakQty = $product->getData('imbusak');
        return ($imbusakQty !== null) ? floatval($imbusakQty) : 0;
    }

    public function getMontaznyKluc(Product $product)
    {
        $montaznyKluc = $product->getData('montazny_kluc');
        return ($montaznyKluc !== null) ? floatval($montaznyKluc) : 0;
    }

    public function getNastavitelneNozicky(Product $product)
    {
        $nastavitelneNozicky = $product->getData('nastavitelne_nozicky');
        return ($nastavitelneNozicky !== null) ? floatval($nastavitelneNozicky) : 0;
    }

    public function getUmeleZaslepky(Product $product)
    {
        $umeleZaslepky = $product->getData('umele_zaslepky');
        return ($umeleZaslepky !== null) ? floatval($umeleZaslepky) : 0;
    }

    public function getSkrutkyM8Imbus(Product $product)
    {
        $skrutkym8imbus = $product->getData('skrutky_m8_imbus');
        return ($skrutkym8imbus !== null) ? floatval($skrutkym8imbus) : 0;
    }

    public function getSkrutkyM8Sesthran(Product $product)
    {
        $skrutkym8sesthran = $product->getData('skrutky_m8_sesthran');
        return ($skrutkym8sesthran !== null) ? floatval($skrutkym8sesthran) : 0;
    }

    public function getFilcovaOchrana(Product $product)
    {
        $filcovaochrana = $product->getData('filcova_ochrana');
        return ($filcovaochrana !== null) ? floatval($filcovaochrana) : 0;
    }



   
	


}