<?php
namespace BigConnect\ProductConfigurator\Plugin\Result;

use Magento\Framework\App\ResponseInterface;

class Page
{
    private $context;
    private $registry;
    private $helper;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $registry,
        \BigConnect\ProductConfigurator\Helper\Data $helper
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->helper = $helper;
    }

    public function beforeRenderResult(
        \Magento\Framework\View\Result\Page $subject,
        ResponseInterface $response
    ){
        if($this->context->getRequest()->getFullActionName() == 'catalog_product_view'){
            $current_product = $this->registry->registry('current_product');
            $productId = $current_product->getId();
            $options = $current_product->getOptions();
            $customOptionTitles = $this->helper->getCustomOptionTitles($productId);

            foreach ($options as $option){
                $defaultStoreTitle = $option->setStoreId(1)->getTitle();

                if (in_array($defaultStoreTitle, $customOptionTitles)) {

                    $subject->getConfig()->addBodyClass('custom-product-configurator');
//                    $subject->getConfig()->addBodyClass('custom-product-configurator custom-class-a-'.$current_product->getSku());
                    break;
                }

            }
        }





        return [$response];
    }
}
