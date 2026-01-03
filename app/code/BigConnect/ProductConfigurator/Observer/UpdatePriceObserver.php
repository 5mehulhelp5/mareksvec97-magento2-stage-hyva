<?php
namespace BigConnect\ProductConfigurator\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteItem;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\StoreManagerInterface; 
use Magento\Directory\Model\CurrencyFactory; 

class UpdatePriceObserver implements ObserverInterface
{
    protected $appEmulation;
    protected $helper;
    protected $storeManager; 
    protected $currencyFactory; 

    public function __construct(
        AppEmulation $appEmulation, 
        \BigConnect\ProductConfigurator\Helper\Data $helper, 
        StoreManagerInterface $storeManager, 
        CurrencyFactory $currencyFactory 
    ) {
        $this->appEmulation = $appEmulation;
        $this->helper = $helper;
        $this->storeManager = $storeManager; 
        $this->currencyFactory = $currencyFactory; 
    }

    public function execute(Observer $observer)
    {
        $store = $this->storeManager->getStore();
        $baseCurrencyCode = $store->getBaseCurrencyCode();
        $currentCurrencyCode = $store->getCurrentCurrencyCode();

        $baseCurrency = $this->currencyFactory->create()->load($baseCurrencyCode);
        $currentCurrency = $this->currencyFactory->create()->load($currentCurrencyCode);
        $rate = $baseCurrency->getRate($currentCurrency);

        // Start store emulation of Store ID 1
        $this->appEmulation->startEnvironmentEmulation(1, \Magento\Framework\App\Area::AREA_FRONTEND, true);

        

        /** @var QuoteItem $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $quoteItem->getProduct();
        $productId = $product->getId();

        // The option titles you want to change the price of
        $optionTitles = $this->helper->getCustomOptionTitles($productId);

        if (!empty($optionTitles)) {
            $options = $product->getOptions();

            if (is_array($options) || is_object($options)) {
                $totalAdditionalPrice = 0;
                $calculationMinMaxData = $this->helper->getCalculationMinMax($productId);
                $dimensionOptionPrices = $this->helper->getDimensionOptionPricesNew($productId);

                foreach($options as $option) {
                    if(in_array($option->getTitle(), $optionTitles)) {
                        $optionId = $option->getOptionId();
                        $optionPrice = $dimensionOptionPrices[$option->getTitle()];  // Get the price set in the admin

                        $productOptions = $product->getTypeInstance(true)->getOrderOptions($product);
                        $customOptions = $productOptions['options'];

                        $minSizeValue = $calculationMinMaxData[$option->getTitle()]['min'];

                        $this->appEmulation->stopEnvironmentEmulation(); // Stop the current emulation
                        $this->appEmulation->startEnvironmentEmulation(1, \Magento\Framework\App\Area::AREA_FRONTEND, true); // Start emulation for Store ID 1

                        $optionValue = null;
                        foreach($customOptions as $customOption) {
                            if ($customOption['option_id'] == $optionId) {
                                $optionValue = $customOption['option_value'];
                                break;
                            }
                        }

                        if ($optionValue !== null) {
                            $additionalPrice = ((($optionValue * $optionPrice) - ($minSizeValue * $optionPrice)) * $rate) ; // Use the price set in the admin
                            $totalAdditionalPrice += $additionalPrice;
                        } else {
                            throw new LocalizedException(__('Option with id: %1 is not set.', $optionId));
                        }
                    }
                }

                if ($totalAdditionalPrice > 0) {
                    $price = ( $product->getFinalPrice() * $rate ) + $totalAdditionalPrice;

                    // Set the custom price
                    $quoteItem->setCustomPrice($price);
                    $quoteItem->setOriginalCustomPrice($price);
                    // Enable super mode on the product.
                    $quoteItem->getProduct()->setIsSuperMode(true);
                }
            } else {
                throw new LocalizedException(__('Product options are not available.'));
            }
        }

        // Stop store emulation
        $this->appEmulation->stopEnvironmentEmulation();
    }
}
