<?php
/**
 * @category  Apptrian
 * @package   Apptrian_PinterestPixel
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://www.apptrian.com/license Proprietary Software License EULA
 */
 
namespace Apptrian\PinterestPixel\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    public $moduleList;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * @var \Apptrian\PinterestPixel\Service\CurrentCustomer
     */
    public $currentCustomer;
    
    /**
     * @var \Apptrian\PinterestPixel\Service\CurrentCategory
     */
    public $currentCategory;
    
    /**
     * @var \Apptrian\PinterestPixel\Service\CurrentProduct
     */
    public $currentProduct;
    
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    public $catalogHelper;
    
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    public $categoryFactory;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;
    
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    public $configurableProductModel;
    
    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    public $bundleProductModel;
    
    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped
     */
    public $groupedProductModel;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    public $checkoutSession;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customerFactory;
    
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    public $addressFactory;
    
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    public $regionFactory;
    
    /**
     * Tax config model
     *
     * @var \Magento\Tax\Model\Config
     */
    public $taxConfig;
    
    /**
     * Tax display flag
     *
     * @var null|int
     */
    public $taxDisplayFlag = null;
    
    /**
     * Tax catalog flag
     *
     * @var null|int
     */
    public $taxCatalogFlag = null;
    
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    public $localeFormat;
    
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    public $localeResolver;
    
    /**
     * Store object
     *
     * @var null|\Magento\Store\Model\Store
     */
    public $store = null;
    
    /**
     * Store ID
     *
     * @var null|int
     */
    public $storeId = null;
    
    /**
     * Base currency code
     *
     * @var null|string
     */
    public $baseCurrencyCode = null;
    
    /**
     * Current currency code
     *
     * @var null|string
     */
    public $currentCurrencyCode = null;
    
    /**
     * Category ID
     *
     * @var int
     */
    public $categoryId = 0;
    
    /**
     * Category event name
     *
     * @var null|string
     */
    public $categoryEventName = null;
    
    /**
     * Search event name
     *
     * @var null|string
     */
    public $searchEventName = null;
    
    /**
     * Search event parameter
     *
     * @var null|string
     */
    public $searchParamName = null;
    
    /**
     * Product type
     *
     * @var string
     */
    public $productType = null;
    
    /**
     * Product ID
     *
     * @var string|integer
     */
    public $productId = 0;
    
    /**
     * Product children  (line_items array with IDs)
     *
     * @var array
     */
    public $lineItemsWithIds = [];
    
    /**
     * Bundle product options to product IDs map
     *
     * @var array
     */
    public $bundleProductOptionsData = [];
    
    /**
     * Configurable product options to product IDs map
     *
     * @var array
     */
    public $configurableProductOptionsData = [];
    
    /**
     * Configurable product allowed products array
     *
     * @var array
     */
    public $allowedProducts = [];
    
    /**
     * User Data.
     *
     * @var array
     */
    public $userData = [];
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Apptrian\PinterestPixel\Service\CurrentCustomer $currentCustomer
     * @param \Apptrian\PinterestPixel\Service\CurrentCategory $currentCategory
     * @param \Apptrian\PinterestPixel\Service\CurrentProduct $currentProduct
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $cP
     * @param \Magento\Bundle\Model\Product\Type $bundleProduct
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProduct
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Apptrian\PinterestPixel\Service\CurrentCustomer $currentCustomer,
        \Apptrian\PinterestPixel\Service\CurrentCategory $currentCategory,
        \Apptrian\PinterestPixel\Service\CurrentProduct $currentProduct,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $cP,
        \Magento\Bundle\Model\Product\Type $bundleProduct,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProduct,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->moduleList               = $moduleList;
        $this->scopeConfig              = $context->getScopeConfig();
        $this->request                  = $context->getRequest();
        $this->storeManager             = $storeManager;
        $this->currentCustomer          = $currentCustomer;
        $this->currentCategory          = $currentCategory;
        $this->currentProduct           = $currentProduct;
        $this->catalogHelper            = $catalogHelper;
        $this->categoryFactory          = $categoryFactory;
        $this->productFactory           = $productFactory;
        $this->configurableProductModel = $cP;
        $this->bundleProductModel       = $bundleProduct;
        $this->groupedProductModel      = $groupedProduct;
        $this->checkoutSession          = $checkoutSession;
        $this->customerFactory          = $customerFactory;
        $this->addressFactory           = $addressFactory;
        $this->regionFactory            = $regionFactory;
        $this->taxConfig                = $taxConfig;
        $this->localeFormat             = $localeFormat;
        $this->localeResolver           = $localeResolver;
        
        parent::__construct($context);
    }
    
    /**
     * Returns extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        $moduleCode = 'Apptrian_PinterestPixel';
        $moduleInfo = $this->moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }
    
    /**
     * Returns Pinterest Pixel ID from config.
     * It might return multiple IDs so there is option
     * to convert it to array.
     *
     * @param int
     * @return array
     */
    public function getPinterestPixelId($toArray = true)
    {
        $id = '';
        
        $id = $this->getConfig(
            'apptrian_pinterestpixel/general/pixel_id',
            $this->getStoreId()
        );
        
        if ($toArray) {
            return explode(',', $id);
        } else {
            return $id;
        }
    }
    
    /**
     * Returns array of handles based on configuration.
     *
     * @param string $type
     * @return array
     */
    public function getPageHandles($type = 'general')
    {
        $handles = [];
        
        $config = $this->getConfig(
            'apptrian_pinterestpixel/' . $type . '/page_handles',
            $this->getStoreId()
        );
        
        $handles = explode(',', $config);
        
        return $handles;
    }
    
    /**
     * Returns customer data for enhanced match.
     *
     * @return array
     */
    public function getUserDataForJs($customerId = 0)
    {
        $data = [];
        
        if ($customerId) {
            $userData = $this->getUserData($customerId);
        } else {
            $userData = $this->getUserDataFromOrder();
        }
        
        foreach ($userData as $key => $value) {
            if ($value) {
                $data[$key] = $this->formatData($value);
            }
        }
        
        return $data;
    }
    
    /**
     * Returns customer data needed for enhanced match.
     *
     * @return array
     */
    public function getUserData($customerId = 0)
    {
        if (!empty($this->userData)) {
            return $this->userData;
        }
        
        $data         = [];
        $address      = null;
        $addressId    = 0;
        $customer     = null;
        
        if (!$customerId) {
            return [];
        }
        
        $customer = $this->getCustomerById($customerId);
        
        if (null == $customer) {
            return [];
        }
        
        $data['external_id'] = $customerId;
        
        $data['db'] = $this->stripNonNumeric(
            $this->datetimeToDate($customer->getDob())
        );
        $data['em'] = $customer->getEmail();
        $data['fn'] = $customer->getFirstname();
        
        // 1 male, 2 female, 3 not specified
        $ge = $customer->getGender();
        
        if ($ge == 1) {
            $data['ge'] =  'm';
        }
        
        if ($ge == 2) {
            $data['ge'] =  'f';
        }
        
        $data['ln'] = $customer->getLastname();
        
        // Get billing address
        $addressId = $customer->getDefaultBilling();
        
        // If there is no billing address get shipping address
        if (!$addressId) {
            $addressId = $customer->getDefaultShipping();
        }
        
        if ($addressId) {
            $address = $this->getCustomerAddressById($addressId);
            
            $data['ct']      = $address->getCity();
            $data['country'] = $address->getCountry();
            $data['ph']      = $this->stripNonNumeric($address->getTelephone());
            $data['st']      = $this->getRegionCodeOrNameById(
                $address->getRegionId()
            );
            $data['zp']      = $address->getPostcode();
        }
        
        $this->userData = $data;
        return $data;
    }
    
    /**
     * Returns customer data needed for enhanced match from order object.
     *
     * @return array
     */
    public function getUserDataFromOrder()
    {
        if (!empty($this->userData)) {
            return $this->userData;
        }
        
        $data         = [];
        $address      = null;
        
        $orderId = $this->checkoutSession->getLastRealOrder()->getId();
        
        if ($orderId) {
            $order = $this->checkoutSession->getLastRealOrder();
            
            $customerId = $order->getCustomerId();
            
            if ($customerId) {
                return $this->getUserData($customerId);
            } else {
                $data['db'] = $this->stripNonNumeric(
                    $this->datetimeToDate($order->getCustomerDob())
                );
                $data['em'] = $order->getCustomerEmail();
                $data['fn'] = $order->getCustomerFirstname();
                
                // 1 male, 2 female, 3 not specified
                $ge = $order->getCustomerGender();
                
                if ($ge == 1) {
                    $data['ge'] =  'm';
                }
                
                if ($ge == 2) {
                    $data['ge'] =  'f';
                }
                
                $data['ln'] = $order->getCustomerLastname();
                
                // Get billing address
                $address = $order->getBillingAddress()->getData();
                
                // If there is no billing address get shipping address
                if (empty($address)) {
                    $address = $order->getShippingAddress()->getData();
                }
                
                if (!empty($address)) {
                    if (isset($address['city'])) {
                        $data['ct'] = $address['city'];
                    }
                    
                    if (isset($address['country_id'])) {
                        $data['country'] = $address['country_id'];
                    }
                    
                    if (isset($address['telephone'])) {
                        $data['ph'] = $this->stripNonNumeric($address['telephone']);
                    }
                    
                    if (isset($address['region_id'])) {
                        $data['st'] = $this->getRegionCodeOrNameById(
                            $address['region_id']
                        );
                    }
                    
                    if (isset($address['postcode'])) {
                        $data['zp'] = $address['postcode'];
                    }
                }
                
                $this->userData = $data;
                return $data;
            }
        }
        
        return $data;
    }
    
    /**
     * Returns category data for tracking.
     *
     * @return array
     */
    public function getCategoryDataForJs()
    {
        $isEnabled = $this->isEventEnabled('pagevisit');
        
        if (!$isEnabled) {
            return [];
        }
        
        $d = [];
        
        $categoryEventName = $this->getCategoryEventName();
        
        if ($categoryEventName) {
            $data = $this->getCategoryData();
            
            if (null == $data) {
                $d['data'] = [];
            } else {
                $d['data'] = $data;
            }
            
            $d['event_name'] = $categoryEventName;
        }
        
        return $d;
    }
    
    /**
     * Returns category data needed for tracking.
     *
     * @return array
     */
    public function getCategoryData($categoryId = 0)
    {
        if ($categoryId) {
            $c = $this->getCategory($categoryId);
        } else {
            $c = $this->getCategory();
        }
        
        if (null == $c) {
            return null;
        }
        
        $data = [];
        
        // Get category ID
        $this->categoryId = $c->getId();
        
        // Get event name
        $eventName = $this->getCategoryEventName();
        
        if ($eventName) {
            // Custom Parameters
            $attributeValue = '';
            $map = $this->getParameterToAttributeMap('category');
            
            foreach ($map as $parameter => $attribute) {
                $attributeValue = $this->getAttributeValue($c, $attribute);
                
                if ($attributeValue) {
                    $data[$parameter] = $this->filter($attributeValue);
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Returns product data needed for tracking.
     *
     * @return array
     */
    public function getProductData($id = 0)
    {
        if ($id) {
            $p = $this->getProduct($id);
        } else {
            $p = $this->getProduct();
        }
        
        if (null == $p) {
            return [];
        }
        
        // Get product data with line_items
        $data = $this->getProductDataWithLineItems($p);
        
        if (null == $data) {
            return [];
        }
        
        $d = [];
        
        $d['data']                              = $data;
        $d['line_items_with_ids']               = $this->lineItemsWithIds;
        $d['bundle_product_options_data']       = $this->bundleProductOptionsData;
        $d['configurable_product_options_data'] = $this->configurableProductOptionsData;
        $d['product_id']                        = $this->productId;
        $d['product_type']                      = $this->productType;
        
        return $d;
    }
    
    /**
     * Returns product data array with line_items
     *
     * @param \Magento\Catalog\Model\Product $p
     * @return array
     */
    public function getProductDataWithLineItems($p)
    {
        $data            = [];
        $lineItems       = [];
        $lineItemsSingle = [];
        $i               = 0;
        
        $currencyCode = $this->getCurrentCurrencyCode();
        $value        = $this->formatPrice($this->getProductPrice($p));
        $sku          = $this->filter($p->getSku());
        $productName  = $this->filter($p->getName());
        $productType  = $p->getTypeId();
        
        // Save product ID and type for easy access
        $this->productId   = $p->getEntityId();
        $this->productType = $productType;
        
        $lineItemsSingle[0]['product_id'] = $sku;
        $lineItemsSingle[0]['product_quantity'] = 1;
        $lineItemsSingle[0]['product_price'] = $value;
        
        // Custom Parameters
        $attributeValue = '';
        $map = $this->getParameterToAttributeMap();
        
        foreach ($map as $parameter => $attribute) {
            $attributeValue = $this->getAttributeValue($p, $attribute);
            
            if ($attributeValue) {
                $lineItemsSingle[0][$parameter] = $this->filter($attributeValue);
            }
        }
        
        $data['line_items'] = $lineItemsSingle;
        
        // Event options
        // 1 = parent
        // 2 = children
        // 3 = both
        $option = (int) $this->getConfig(
            'apptrian_pinterestpixel/product/ident_' . $productType,
            $this->getStoreId()
        );
        
        // Check product type and find all variant SKUs
        if ($productType == 'configurable'
            || $productType == 'bundle'
            || $productType == 'grouped'
        ) {
            $children = $this->getProductChildren($p);
            $childId  = 0;
            $childSKu = '';
            
            foreach ($children as $child) {
                // Must load child product to get all data
                $child = $this->getProductById($child->getEntityId());
                
                if ($productType == 'configurable') {
                    $this->allowedProducts[$i] = $child;
                }
                
                $childId  = $this->filter($child->getEntityId());
                $childSku = $this->filter($child->getSku());
                
                // Required parameter id
                $lineItems[$i]['product_id'] = $childSku;
                $this->lineItemsWithIds[$childId]['product_id'] = $childSku;
                if ($option == 3) {
                    // Optional parameter item_group_id
                    $lineItems[$i]['item_group_id'] = $sku;
                    $this->lineItemsWithIds[$childId]['item_group_id'] = $sku;
                }
                
                // Required parameter quantity
                $lineItems[$i]['product_quantity'] = 1;
                $this->lineItemsWithIds[$childId]['product_quantity'] = 1;
                
                // Optional parameter item_price
                $lineItems[$i]['product_price'] = $this->formatPrice($this->getProductPrice($child));
                $this->lineItemsWithIds[$childId]['product_price'] = $this->formatPrice($this->getProductPrice($child));
                
                // Optional custom parameters
                foreach ($map as $parameter => $attribute) {
                    $attributeValue = $this->getAttributeValue($child, $attribute);
                    
                    if ($attributeValue) {
                        $lineItems[$i][$parameter] = $this->filter($attributeValue);
                        $this->lineItemsWithIds[$childId][$parameter] = $this->filter($attributeValue);
                    }
                }
                
                // Do not forget to increment
                $i++;
            }
            
            // Must be done like this because you need lineItemsWithIds in any case
            if ($option !== 1) {
                // Reset line_items
                $data['line_items'] = [];
                // Set line_items
                $data['line_items'] = $lineItems;
            }
            
            // If bundle product type set options array
            if ($productType == 'bundle') {
                $this->bundleProductOptionsData = $this->getBundleProductOptionsData($p);
            }
            
            // If configurable product type set options array
            if ($productType == 'configurable') {
                $this->configurableProductOptionsData = $this->getConfigurableProductOptionsData($p);
            }
        } else {
            // simple, downlodable, virtual
            if ($option == 3) {
                $parentSku = $this->getParentProductSku($p->getEntityId());
                
                if ($parentSku) {
                    // Optional parameter item_group_id
                    $data['line_items'][0]['item_group_id'] = $parentSku;
                }
            }
        }
        
        $data['value']              = $value;
        $data['currency']           = $currencyCode;
        
        return $data;
    }
    
    /**
     * Returns configuration value for event.
     *
     * @return bool
     */
    public function isEventEnabled($event)
    {
        $evt = $this->convertToLowercase($event);
        
        $enabled = (bool) $this->getConfig(
            'apptrian_pinterestpixel/general/enabled',
            $this->getStoreId()
        );
        
        $config = (bool) $this->getConfig(
            'apptrian_pinterestpixel/general/' . $evt . '_enabled',
            $this->getStoreId()
        );
        
        if ($enabled) {
            return $config;
        } else {
            return false;
        }
    }
    
    /**
     * Returns configuration value for PageView with all.
     *
     * @return bool
     */
    public function isPageWithAll()
    {
        return (int) $this->getConfig(
            'apptrian_pinterestpixel/general/page_all',
            $this->getStoreId()
        );
    }
    
    /**
     * Returns configuration value for detect_selected_sku
     *
     * @return bool
     */
    public function isDetectSelectedSkuEnabled($productType)
    {
        $path = 'apptrian_pinterestpixel/general/';
        
        if ($productType == 'bundle'
            || $productType == 'configurable'
            || $productType == 'grouped'
        ) {
            $path .= 'detect_selected_sku_' . $productType;
        } else {
            $path .= 'detect_selected_sku';
        }
        
        return (bool) $this->getConfig($path, $this->getStoreId());
    }
    
    /**
     * Returns configuration value for Pinterest Pixel.
     *
     * @return bool
     */
    public function isPixelEnabled()
    {
        return (bool) $this->getConfig(
            'apptrian_pinterestpixel/general/enabled',
            $this->getStoreId()
        );
    }
    
    /**
     * Returns configuration value for Pixel base code.
     *
     * @return bool
     */
    public function isBaseCodeEnabled()
    {
        return (bool) $this->getConfig(
            'apptrian_pinterestpixel/general/base_code_enabled',
            $this->getStoreId()
        );
    }
    
    /**
     * Returns configuration value for noscript.
     *
     * @return bool
     */
    public function isNoScriptEnabled()
    {
        return (bool) $this->getConfig(
            'apptrian_pinterestpixel/general/noscript_enabled',
            $this->getStoreId()
        );
    }
    
    /**
     * Returns price decimal sign
     *
     * @return string
     */
    public function getPriceDecimalSymbol()
    {
        $decimalSymbol = '';
        $locale        = $this->localeResolver->getLocale();
        $priceFormat   = $this->localeFormat->getPriceFormat($locale);
        $decimalSymbol = $priceFormat['decimalSymbol'];
        
        return $decimalSymbol;
    }
    
    /**
     * Returns order data for tracking.
     *
     * @return array
     */
    public function getOrderData()
    {
        $isEnabled = $this->isEventEnabled('checkout');
        
        if (!$isEnabled) {
            return [];
        }
        
        $data = $this->getOrderOrQuoteData('order');
        
        if (null == $data) {
            return [];
        }
        
        $d = [];
        
        $eventName = 'checkout';
        
        $d['data']       = $data;
        $d['event_name'] = $eventName;
        
        return $d;
    }
    
    /**
     * Returns quote data for tracking.
     *
     * @return array
     */
    public function getQuoteData()
    {
        $isEnabled = $this->isEventEnabled('initiatecheckout');
        
        if (!$isEnabled) {
            return [];
        }
        
        $data = $this->getOrderOrQuoteData('quote');
        
        if (null == $data) {
            return [];
        }
        
        $d = [];
        
        $eventName = 'initiatecheckout';
        
        $d['data']       = $data;
        $d['event_name'] = $eventName;
        
        return $d;
    }
    
    /**
     * Returns data needed for tracking based on config group.
     *
     * @return array|null
     */
    public function getOrderOrQuoteData($group)
    {
        $obj = null;
        
        if ($group == 'order') {
            $obj = $this->checkoutSession->getLastRealOrder();
        }
        
        if ($group == 'quote') {
            $obj = $this->checkoutSession->getQuote();
        }
        
        if (null == $obj) {
            return null;
        }
        
        $objId = $obj->getId();
        
        if (!$objId) {
            return null;
        }
        
        $allItems        = $obj->getAllItems();
        $allVisibleItems = $obj->getAllVisibleItems();

        $data         = [];
        $items        = [];
        $itemId       = '';
        $parentItemId = '';
        $i            = 0;
        $lineItems     = [];
        $product      = null;
        $productId    = 0;
        $productType  = '';
        $parent       = null;
        $parentId     = 0;
        $storeId      = $this->getStoreId();
        $numItems     = 0;
        $taxFlag      = $this->getDisplayTaxFlag();

        // Custom Parameters
        $attributeValue = '';
        $map = $this->getParameterToAttributeMap($group);

        foreach ($allVisibleItems as $item) {
            $itemId = $item->getItemId();
            
            $items[$itemId]['item_id']        = $itemId;
            $items[$itemId]['parent_item_id'] = $item->getParentItemId();
            $items[$itemId]['product_id']     = $item->getProductId();
            $items[$itemId]['product_type']   = $item->getProductType();
            $items[$itemId]['sku']            = $this->filter($item->getSku());
            $items[$itemId]['name']           = $this->filter($item->getName());
            $items[$itemId]['store_id']       = $item->getStoreId();
            
            if ($taxFlag) {
                $items[$itemId]['price'] = $this->formatPrice($item->getPriceInclTax());
            } else {
                $items[$itemId]['price'] = $this->formatPrice($item->getPrice());
            }
            
            if ($group == 'quote') {
                $items[$itemId]['qty'] = round($item->getQty(), 0);
            } else {
                $items[$itemId]['qty'] = round($item->getQtyOrdered(), 0);
            }
        }

        foreach ($allItems as $item) {
            $itemId       = $item->getItemId();
            $parentItemId = $item->getParentItemId();
            
            if ($parentItemId) {
                $items[$parentItemId]['children'][$itemId]['item_id']        = $itemId;
                $items[$parentItemId]['children'][$itemId]['parent_item_id'] = $parentItemId;
                $items[$parentItemId]['children'][$itemId]['product_id']     = $item->getProductId();
                $items[$parentItemId]['children'][$itemId]['product_type']   = $item->getProductType();
                $items[$parentItemId]['children'][$itemId]['sku']            = $this->filter($item->getSku());
                $items[$parentItemId]['children'][$itemId]['name']           = $this->filter($item->getName());
                $items[$parentItemId]['children'][$itemId]['store_id']       = $item->getStoreId();
                
                if ($taxFlag) {
                    $items[$parentItemId]['children'][$itemId]['price'] = $this->formatPrice($item->getPriceInclTax());
                } else {
                    $items[$parentItemId]['children'][$itemId]['price'] = $this->formatPrice($item->getPrice());
                }
                
                if ($group == 'quote') {
                    if ($items[$parentItemId]['product_type'] == 'configurable') {
                        $q = $items[$parentItemId]['qty'];
                        $items[$parentItemId]['children'][$itemId]['qty'] = $q;
                    } else {
                        $items[$parentItemId]['children'][$itemId]['qty'] = round($item->getQty(), 0);
                    }
                } else {
                    $items[$parentItemId]['children'][$itemId]['qty'] = round($item->getQtyOrdered(), 0);
                }
            }
        }
        
        foreach ($items as $item) {
            $productId   = $item['product_id'];
            $productType = $item['product_type'];
            $storeId     = $item['store_id'];
            
            // Event options
            // 1 = poduct/parent
            // 2 = children/child
            // 3 = children/child/product and parent
            $option = (int) $this->getConfig(
                'apptrian_pinterestpixel/' . $group . '/ident_' . $productType,
                $storeId
            );
            
            $product    = $this->getProductById($productId, $storeId);
            $productSku = $this->filter($product->getSku());
            
            if ($productType == 'bundle' || $productType == 'configurable') {
                if ($option == 1) {
                    // Option 1 means show parent SKU only
                    $qty = $item['qty'];
                    $lineItems[$i]['product_id']       = $productSku;
                    $lineItems[$i]['product_quantity'] = $qty;
                    $lineItems[$i]['product_price']    = $item['price'];
                    
                    // Custom Parameters
                    $lineItems[$i] = $this->addCustomParameters($map, $product, $lineItems[$i]);
                    
                    $numItems += $qty;
                    
                    $i++;
                } else {
                    // Option 2. or 3. means show children SKUs
                    
                    $children  = $item['children'];
                    
                    foreach ($children as $child) {
                        $childProductId = $child['product_id'];
                        $childProduct   = $this->getProductById($childProductId, $storeId);
                        
                        $qty = $child['qty'];
                        
                        if ($productType == 'bundle') {
                            // Budle products may have global qty higher than 1
                            $parentItemId = $child['parent_item_id'];
                            $globalQty    = $items[$parentItemId]['qty'];
                            $qty          = $qty * $globalQty;
                        }
                        
                        $lineItems[$i]['product_id'] = $this->filter($childProduct->getSku());
                        $lineItems[$i]['product_quantity'] = $qty;
                        $lineItems[$i]['product_price'] = $child['price'];
                        
                        // For configurable you must use congfigurable price
                        // child price is 0
                        if ($productType == 'configurable') {
                            $lineItems[$i]['product_price'] = $item['price'];
                        }
                        
                        if ($option == 3) {
                            // Option 3 add parent product SKU on children
                            $lineItems[$i]['item_group_id'] = $productSku;
                        }
                        
                        // Custom Parameters
                        $lineItems[$i] = $this->addCustomParameters($map, $childProduct, $lineItems[$i]);
                        
                        $numItems += $qty;
                        
                        $i++;
                    }
                }
            } else {
                // grouped, simple, virtual, downloadable products
                
                $qty = $item['qty'];
                $lineItems[$i]['product_id']       = $productSku;
                $lineItems[$i]['product_quantity'] = $qty;
                $lineItems[$i]['product_price']    = $item['price'];
                
                // Reset parent ID
                $parentId = 0;
                
                if ($productType == 'grouped') {
                    if ($option == 3) {
                        // Get parent grouped product ID
                        $parentId = $this->getParentGroupedProductId($productId);
                    }
                } else {
                    if ($option == 2) {
                        // Get parent product ID
                        $parentId = $this->getParentProductId($productId);
                    }
                }
                
                if ($parentId) {
                    $parent = $this->getProductById($parentId, $storeId);
                    if ($parent) {
                        $lineItems[$i]['item_group_id'] = $this->filter($parent->getSku());
                    }
                }
                
                // Custom Parameters
                $lineItems[$i] = $this->addCustomParameters($map, $product, $lineItems[$i]);
                
                $numItems += $qty;
                
                $i++;
            }
        }
        
        // Check if there are items and if not return null
        if (empty($lineItems)) {
            return null;
        }
        
        // Order ID
        $orderIdParam = (string) $this->getConfig(
            'apptrian_pinterestpixel/' . $group . '/order_id_param',
            $storeId
        );
        if ($orderIdParam) {
            $data[$orderIdParam] = (string) $obj->getId();
        }
        
        // Order increment ID
        $orderIncrementIdParam = (string) $this->getConfig(
            'apptrian_pinterestpixel/' . $group . '/order_increment_id_param',
            $storeId
        );
        if ($orderIncrementIdParam) {
            $data[$orderIncrementIdParam] = (string) $obj->getIncrementId();
        }
        
        // Quote ID
        $quoteIdParam = (string) $this->getConfig(
            'apptrian_pinterestpixel/' . $group . '/quote_id_param',
            $storeId
        );
        if ($quoteIdParam) {
            if ($group == 'quote') {
                $data[$quoteIdParam] = (string) $obj->getId();
            } else {
                $data[$quoteIdParam] = (string) $obj->getQuoteId();
            }
        }
        
        $data['line_items'] = $lineItems;
        $data['order_quantity'] = $numItems;
        $data['value'] = $this->formatPrice($obj->getGrandTotal());
        
        if ($group == 'quote') {
            $data['currency'] = $obj->getQuoteCurrencyCode();
        } else {
            $data['currency'] = $obj->getOrderCurrencyCode();
        }
        
        return $data;
    }
    
    /**
     * Adds custom parameters to line item.
     *
     * @param array $map
     * @param \Magento\Catalog\Model\Product $product
     * @param array $lineItem
     * @return array
     */
    public function addCustomParameters($map, $product, $lineItem)
    {
        foreach ($map as $parameter => $attribute) {
            $attributeValue = $this->getAttributeValue(
                $product,
                $attribute
            );
            
            if ($attributeValue) {
                $lineItem[$parameter] = $this->filter($attributeValue);
            }
        }
        
        return $lineItem;
    }
    
    /**
     * Returns search data for tracking.
     *
     * @return array
     */
    public function getSearchDataForJs()
    {
        $isEnabled = $this->isEventEnabled('search');
        
        if (!$isEnabled) {
            return [];
        }
        
        $d = [];
        
        $searchEventName = $this->getSearchEventName();
        $data            = $this->getSearchData();
        
        if ($searchEventName && !empty($data)) {
            $searchParamName = $this->getSearchParamName();
            $searchString    = $data[$searchParamName];
            
            $d['data']       = $data;
            $d['event_name'] = $searchEventName;
        }
        
        return $d;
    }
    
    /**
     * Returns search data needed for tracking.
     *
     * @return array|null
     */
    public function getSearchData()
    {
        $data = [];
        
        $requestParams = explode(
            ',',
            $this->getConfig(
                'apptrian_pinterestpixel/search/request_params',
                $this->getStoreId()
            )
        );
        
        $searchStrings = [];
        $p = '';
        
        foreach ($requestParams as $param) {
            // If prameter is array
            if (strpos($param, '[') !== false) {
                $p = substr($param, 0, strpos($param, '['));
            } else {
                $p = $param;
            }
            
            $rp = $this->request->getParam($p);
            
            if (!empty($rp)) {
                if (is_array($rp)) {
                    $v = trim(implode(',', $rp), ',');
                    if (!empty($v)) {
                        $searchStrings[] = $v;
                    }
                }
                
                if (is_string($rp)) {
                    $searchStrings[] = $this->filter(trim($rp));
                }
            }
        }
        
        $paramName    = $this->getSearchParamName();
        $searchString = implode(',', $searchStrings);

        if ($paramName && $searchString) {
            $data[$paramName] = $searchString;
        }
        
        return $data;
    }
    
    /**
     * Returns configuration value for event name
     *
     * @param string $group
     * @return string
     */
    public function getEventName($group)
    {
        return $this->filter(
            (string) $this->getConfig(
                'apptrian_pinterestpixel/' . $group . '/event_name',
                $this->getStoreId()
            )
        );
    }
    
    /**
     * Returns configuration value for category event name
     *
     * @return string
     */
    public function getCategoryEventName()
    {
        if ($this->categoryEventName === null) {
            $this->categoryEventName = $this->getEventName('category');
        }
        
        return $this->categoryEventName;
    }
    
    /**
     * Returns configuration value for search event name
     *
     * @return string
     */
    public function getSearchEventName()
    {
        if ($this->searchEventName === null) {
            $this->searchEventName = $this->getEventName('search');
        }
        
        return $this->searchEventName;
    }
    
    /**
     * Returns configuration value for event param
     *
     * @param string $group
     * @return string
     */
    public function getParamName($group)
    {
        return $this->filter(
            (string) $this->getConfig(
                'apptrian_pinterestpixel/' . $group . '/param_name',
                $this->getStoreId()
            )
        );
    }
    
    /**
     * Returns configuration value for search event name
     *
     * @return string
     */
    public function getSearchParamName()
    {
        if ($this->searchParamName === null) {
            $this->searchParamName = $this->getParamName('search');
        }
        
        return $this->searchParamName;
    }
    
    /**
     * Based on provided configuration path returns configuration value.
     *
     * @param string $configPath
     * @param string|int $scope
     * @return string
     */
    public function getConfig($configPath, $scope = 'default')
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scope
        );
    }
    
    /**
     * Removes new lines and tabs from the string
     * and prepares string.
     *
     * @param string $str
     * @return string
     */
    public function filter($str)
    {
        return trim(str_replace(["\t","\n","\r\n","\r"], '', $str));
    }
    
    /**
     * Returns store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->store === null) {
            $this->store = $this->storeManager->getStore();
        }
        
        return $this->store;
    }
    
    /**
     * Returns Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->getStore()->getId();
        }
        
        return $this->storeId;
    }
    
    /**
     * Returns base currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        if ($this->baseCurrencyCode === null) {
            $this->baseCurrencyCode = strtoupper(
                $this->getStore()->getBaseCurrencyCode()
            );
        }
        
        return $this->baseCurrencyCode;
    }
    
    /**
     * Returns current currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if ($this->currentCurrencyCode === null) {
            $this->currentCurrencyCode = strtoupper(
                $this->getStore()->getCurrentCurrencyCode()
            );
        }
        
        return $this->currentCurrencyCode;
    }
    
    /**
     * Returns flag based on "Stores > Cofiguration > Sales > Tax
     * > Price Display Settings > Display Product Prices In Catalog"
     * Returns 0 or 1 instead of 1, 2, 3.
     *
     * @return int
     */
    public function getDisplayTaxFlag()
    {
        if ($this->taxDisplayFlag === null) {
            // Tax Display
            // 1 - excluding tax
            // 2 - including tax
            // 3 - including and excluding tax
            $flag = $this->taxConfig->getPriceDisplayType($this->getStoreId());
            
            // 0 means price excluding tax, 1 means price including tax
            if ($flag == 1) {
                $this->taxDisplayFlag = 0;
            } else {
                $this->taxDisplayFlag = 1;
            }
        }
        
        return $this->taxDisplayFlag;
    }
    
    /**
     * Returns Stores > Cofiguration > Sales > Tax > Calculation Settings
     * > Catalog Prices configuration value
     *
     * @return int
     */
    public function getCatalogTaxFlag()
    {
        // Are catalog product prices with tax included or excluded?
        if ($this->taxCatalogFlag === null) {
            $this->taxCatalogFlag = (int) $this->getConfig(
                'tax/calculation/price_includes_tax',
                $this->getStoreId()
            );
        }
        
        // 0 means excluded, 1 means included
        return $this->taxCatalogFlag;
    }
    
    /**
     * Returns product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice($product)
    {
        $price = 0.0;
        
        switch ($product->getTypeId()) {
            case 'bundle':
                $price =  $this->getBundleProductPrice($product);
                break;
            case 'configurable':
                $price = $this->getConfigurableProductPrice($product);
                break;
            case 'grouped':
                $price = $this->getGroupedProductPrice($product);
                break;
            default:
                $price = $this->getFinalPrice($product);
        }
        
        return $price;
    }
    
    /**
     * Returns bundle product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBundleProductPrice($product)
    {
        $includeTax = (bool) $this->getDisplayTaxFlag();
        
        return $this->getFinalPrice(
            $product,
            $product->getPriceModel()->getTotalPrices(
                $product,
                'min',
                $includeTax,
                1
            )
        );
    }
    
    /**
     * Returns configurable product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getConfigurableProductPrice($product)
    {
        if ($product->getFinalPrice() === 0) {
            $simpleCollection = $product->getTypeInstance()
                ->getUsedProducts($product);
            
            foreach ($simpleCollection as $simpleProduct) {
                if ($simpleProduct->getPrice() > 0) {
                    return $this->getFinalPrice($simpleProduct);
                }
            }
        }
        
        return $this->getFinalPrice($product);
    }
    
    /**
     * Returns grouped product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getGroupedProductPrice($product)
    {
        $assocProducts = $product->getTypeInstance(true)
            ->getAssociatedProductCollection($product)
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('tax_percent');
        
        $minPrice = INF;
        foreach ($assocProducts as $assocProduct) {
            $minPrice = min($minPrice, $this->getFinalPrice($assocProduct));
        }
        
        return $minPrice;
    }
    
    /**
     * Returns final price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $price
     * @return string
     */
    public function getFinalPrice($product, $price = null)
    {
        if ($price === null) {
            $price = $product->getFinalPrice();
        }
        
        if ($price === null) {
            $price = $product->getData('special_price');
        }
        
        $productType = $product->getTypeId();
        
        // 1. Convert to current currency if needed
        
        // Convert price if base and current currency are not the same
        // Except for configurable products they already have currency converted
        if (($this->getBaseCurrencyCode() !== $this->getCurrentCurrencyCode())
            && $productType != 'configurable'
        ) {
            // Convert to from base currency to current currency
            $price = $this->getStore()->getBaseCurrency()
                ->convert($price, $this->getCurrentCurrencyCode());
        }
        
        // 2. Apply tax if needed
        
        // Simple, Virtual, Downloadable products price is without tax
        // Grouped products have associated products without tax
        // Bundle products price already have tax included/excluded
        // Configurable products price already have tax included/excluded
        if ($productType != 'configurable' && $productType != 'bundle') {
            // If display tax flag is on and catalog tax flag is off
            if ($this->getDisplayTaxFlag() && !$this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    true,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    false,
                    false
                );
            }
        }
        
        // Case when catalog prices are with tax but display tax is set to
        // to exclude tax. Applies for all products except for bundle
        if ($productType != 'bundle') {
            // If display tax flag is off and catalog tax flag is on
            if (!$this->getDisplayTaxFlag() && $this->getCatalogTaxFlag()) {
                $price = $this->catalogHelper->getTaxPrice(
                    $product,
                    $price,
                    false,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    true,
                    false
                );
            }
        }
        
        return $price;
    }
    
    /**
     * Returns formated price.
     *
     * @param string $price
     * @param bool $toFloat
     * @param string $currencyCode
     * @return string
     */
    public function formatPrice($price, $toFloat = true, $currencyCode = '')
    {
        $formatedPrice = number_format($price, 2, '.', '');
        
        if ($currencyCode) {
            return $formatedPrice . ' ' . $currencyCode;
        } else {
            if ($toFloat) {
                return round((float)$formatedPrice, 2);
            } else {
                return $formatedPrice;
            }
        }
    }
    
    /**
     * Returns object attribute value or values. Third param is optional, if
     * set to false it will return array of values for multiselect attributes.
     *
     * @param \Magento\Catalog\Model\Category|\Magento\Catalog\Model\Product $o
     * @param string $attrCode
     * @param bool $toString
     * @return string
     */
    public function getAttributeValue($o, $attrCode, $toString = true)
    {
        $attrValue = '';
        
        if ($o->getData($attrCode)) {
            $attrValue = $o->getAttributeText($attrCode);
            
            if (!$attrValue) {
                $attrValue = $o->getData($attrCode);
            }
        }
        
        if ($toString && is_array($attrValue)) {
            $attrValue = implode(', ', $attrValue);
        }
        
        return $attrValue;
    }
    
    /**
     * Returns array map from parameter mapping configuration.
     * Default is 'product' but you can specify for mapping others.
     *
     * @return array
     */
    public function getParameterToAttributeMap($type = 'product')
    {
        $map = [];
        
        $data = $this->getConfig(
            'apptrian_pinterestpixel/' . $type . '/mapping',
            $this->getStoreId()
        );
        
        if (!$data) {
            return $map;
        }
        
        $pairs = explode('|', $data);
        
        foreach ($pairs as $pair) {
            $pairArray = explode('=', $pair);
            
            if (isset($pairArray[0]) && isset($pairArray[1])) {
                $cleanedParameter = trim($pairArray[0]);
                $cleanedAttribute = trim($pairArray[1]);
                
                if ($cleanedParameter && $cleanedAttribute) {
                    $map[$cleanedParameter] = $cleanedAttribute;
                }
            }
        }
        
        return $map;
    }
    
    /**
     * Returns category object. If ID is not supplied it will return
     * current category.
     *
     * @param null|int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($id = null, $storeId = '')
    {
        $category = null;
        
        if ($id) {
            $category = $this->getCategoryById($id, $storeId);
        } else {
            $category = $this->currentCategory->getCategory();
        }
        
        return $category;
    }
    
    /**
     * Returns category object loaded by ID.
     * Used in getCategoryData() method to retreive category attributes.
     *
     * @param int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategoryById($id, $storeId = '')
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        
        return $this->categoryFactory->create()->setStoreId($storeId)->load($id);
    }
    
    /**
     * Returns product object. If ID is not supplied it will return
     * current product.
     *
     * @param null|int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($id = null, $storeId = '')
    {
        $product = null;
        
        if ($id) {
            $product = $this->getProductById($id, $storeId);
        } else {
            $product = $this->currentProduct->getProduct();
        }
        
        return $product;
    }
    
    /**
     * Returns product object loaded by ID.
     * Used in getOrderOrQuoteData() method to retreive product attributes.
     *
     * @param int $id
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductById($id, $storeId = '')
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        
        return $this->productFactory->create()->setStoreId($storeId)->load($id);
    }
    
    /**
     * Returns product object loaded by SKU.
     * Used in getOrderOrQuoteData() method to retreive product attributes.
     *
     * @param string $sku
     * @param int|string $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductBySku($sku, $storeId = '')
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }
        
        $product = $this->productFactory->create();
        return $product->setStoreId($storeId)->load($product->getIdBySku($sku));
    }
    
    /**
     * Returns product children collection
     * Used in getOrderOrQuoteData() method to retreive product attributes.
     *
     * @param \Magento\Catalog\Model\Product $p
     * @return array|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductChildren($p)
    {
        $children = [];
        $productType = $p->getTypeId();
        
        switch ($productType) {
            case 'bundle':
                $children = $p->getTypeInstance(true)->getSelectionsCollection(
                    $p->getTypeInstance(true)->getOptionsIds($p),
                    $p
                );
                break;
            case 'configurable':
                $children = $p->getTypeInstance()->getUsedProducts($p);
                break;
            case 'grouped':
                $children = $p->getTypeInstance()->getAssociatedProducts($p);
                break;
            default:
                $children = [];
        }
        
        return $children;
    }
    
    /**
     * Returns bundle product options data used for detection of selected SKUs.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getBundleProductOptionsData($product)
    {
        $optionsData = [];
        
        // Get all the selection products used in bundle product
        $selectionCollection = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            );
        
        $selectionArray = [];
        
        foreach ($selectionCollection as $selection) {
            $selectionArray['product_id'] = $selection->getProductId();
            $selectionArray['product_quantity'] = (float) $selection->getSelectionQty();
            $optionsData[$selection->getOptionId()][$selection->getSelectionId()] = $selectionArray;
        }
        
        return $optionsData;
    }
    
    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getConfigurableProductOptionsData($product)
    {
        $options = [];
        
        if (empty($this->allowedProducts)) {
            $this->allowedProducts = $this->getAllowedProducts($product);
        }
        
        $allowAttributes = $this->getAllowedAttributes($product);

        foreach ($this->allowedProducts as $p) {
            $productId = $p->getId();
            foreach ($allowAttributes as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $p
                    ->getData($productAttribute->getAttributeCode());
                
                $options[$productId][$productAttributeId] = $attributeValue;
            }
        }
        
        return $options;
    }
    
    /**
     * Get allowed attributes
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAllowedAttributes($product)
    {
        return $product->getTypeInstance()->getConfigurableAttributes($product);
    }
    
    /**
     * Get Allowed Products
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getAllowedProducts($product)
    {
        $products = [];
        
        $allProducts = $product->getTypeInstance()->getUsedProducts($product);
        
        foreach ($allProducts as $p) {
            $products[] = $p;
        }
        
        return $products;
    }
    
    /**
     * Based on product ID returns parent product SKU
     * or empty string for no parent sku.
     *
     * @param int $productId
     * @return string
     */
    public function getParentProductSku($productId)
    {
        $parentSku = '';
        
        $parentId = $this->getParentProductId($productId);
                
        if ($parentId) {
            $parent = $this->getProductById($parentId);
            if ($parent) {
                $parentSku = $this->filter($parent->getSku());
            }
        }
        
        return $parentSku;
    }
    
    /**
     * Based on product ID returns parent product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentProductId($productId)
    {
        $parentId = null;
        
        // Configurable
        $parentId = $this->getParentConfigurableProductId($productId);
        if ($parentId) {
            return $parentId;
        }
        
        // Bundle
        $parentId = $this->getParentBundleProductId($productId);
        if ($parentId) {
            return $parentId;
        }
        
        // Grouped
        $parentId = $this->getParentGroupedProductId($productId);
        if ($parentId) {
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Based on product ID returns parent bundle product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentBundleProductId($productId)
    {
        $parentId = null;
        
        // Bundle
        $parentIds = $this->bundleProductModel
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Based on product ID returns parent configurable product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentConfigurableProductId($productId)
    {
        $parentId = null;
        
        // Configurable
        $parentIds = $this->configurableProductModel
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Based on product ID returns parent grouped product ID or null for no parent.
     *
     * @param int $productId
     * @return null|int
     */
    public function getParentGroupedProductId($productId)
    {
        $parentId = null;
        
        // Grouped
        $parentIds = $this->groupedProductModel
            ->getParentIdsByChild($productId);
        
        if (!empty($parentIds) && isset($parentIds[0])) {
            $parentId = $parentIds[0];
            return $parentId;
        }
        
        return $parentId;
    }
    
    /**
     * Returns customer object loaded by customer ID.
     *
     * @param int $id
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomerById($id)
    {
        if (!empty($id)) {
            return $this->customerFactory->create()->load($id);
        }
        
        return null;
    }
    
    /**
     * Returns address object loaded by address ID.
     *
     * @param int $id
     * @return \Magento\Customer\Model\Address
     */
    public function getCustomerAddressById($id)
    {
        return $this->addressFactory->create()->load($id);
    }
    
    /**
     * Returns region object loaded by region ID.
     *
     * @param int $id
     * @return \Magento\Directory\Model\Region
     */
    public function getRegionById($id)
    {
        return $this->regionFactory->create()->load($id);
    }
    
    /**
     * Returns region 2 letter code or name based on provided region ID.
     *
     * @param int $id
     * @return string
     */
    public function getRegionCodeOrNameById($id)
    {
        if (!$id) {
            return '';
        }
        
        $region = $this->getRegionById($id);
        $code   = $region->getCode();
        $name   = $region->getDefaultName();
        
        // FB wants only 2 letter codes otherwise name
        if ($this->stringLength($code) == 2) {
            return $code;
        } else {
            return $name;
        }
    }
    
    /**
     * Converts datetime string to date string.
     *
     * @param string $datetimeString
     * @return string
     */
    public function datetimeToDate($datetimeString)
    {
        $date = '';
        
        if ($datetimeString) {
            $date = date('Y-m-d', strtotime($datetimeString));
        }
        
        return $date;
    }
    
    /**
     * Returns string length.
     *
     * @param string $str
     * @return int
     */
    public function stringLength($str)
    {
        if (function_exists('mb_strlen')) {
            $length = mb_strlen($str, 'UTF-8');
        } else {
            $length = strlen($str);
        }
        
        return (int) $length;
    }
    
    /**
     * Converts string to lowercase.
     *
     * @param string $s
     * @return string
     */
    public function convertToLowercase($s)
    {
        if (function_exists('mb_strtolower')) {
            $str = mb_strtolower($s, 'UTF-8');
        } else {
            $str = strtolower($s);
        }
        
        return $str;
    }

    /**
     * Strips all non numeric characters.
     *
     * @param string $str
     * @return string
     */
    public function stripNonNumeric($str)
    {
        return preg_replace('/[^\p{N}]+/', '', $str);
    }
    
    /**
     * Strips all spaces and converts to lowercase.
     *
     * @param string $str
     * @return string
     */
    public function formatData($str)
    {
        return $this->filter(
            $this->convertToLowercase(
                preg_replace('/\s+/', '', $str)
            )
        );
    }
    
    /**
     * Returns data for registration event.
     *
     * @return array
     */
    public function getDataForRegistrationEvent()
    {
        if (!$this->isEventEnabled('signup')) {
            return [];
        }
        
        $d    = [];
        $data = [];
        
        $eventName = 'signup';
        
        $data['lead_type'] = 'New Customer Registration';
        
        $d['data']       = $data;
        $d['event_name'] = $eventName;
        
        return $d;
    }
    
    /**
     * Returns configuration value for fp_cookie.
     *
     * @return boolean
     */
    public function isFpCookie()
    {
        return (bool) $this->getConfig(
            'apptrian_pinterestpixel/general/fp_cookie',
            $this->getStoreId()
        );
    }
    
    /**
     * Returns configuration value for md_frequency.
     *
     * @return boolean
     */
    public function isMdFrequency()
    {
        return (bool) $this->getConfig(
            'apptrian_pinterestpixel/general/md_frequency',
            $this->getStoreId()
        );
    }
}
