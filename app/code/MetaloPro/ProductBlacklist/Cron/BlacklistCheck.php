<?php
namespace MetaloPro\ProductBlacklist\Cron;

use MetaloPro\ProductBlacklist\Logger\BlacklistLogger;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Config\ScopeConfigInterface;

class BlacklistCheck
{
    protected $blacklistLogger;
    protected $productRepository;
    protected $storeManager;
    protected $appState;
    protected $scopeConfig;

    public function __construct(
        BlacklistLogger $blacklistLogger,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        State $appState,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->blacklistLogger = $blacklistLogger;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;

        // Nastavenie kódu oblasti pri inicializácii objektu
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            // Area code už môže byť nastavený, takže chybu môžeme ignorovať
        }
    }

    public function execute()
    {
        // Načítanie ID produktov na čiernej listine z konfigurácie
        $blacklistedProductIds = $this->scopeConfig->getValue(
            'product_blacklist/settings/blacklisted_products',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (is_string($blacklistedProductIds) && !empty($blacklistedProductIds)) {
            $blacklistedProductIds = array_map('trim', explode(',', $blacklistedProductIds));
        } else {
            $blacklistedProductIds = [];
        }

        // Načítanie kódov webov na čiernej listine z konfigurácie
        $blacklistedWebsites = $this->scopeConfig->getValue(
            'product_blacklist/settings/blacklisted_websites',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (is_string($blacklistedWebsites) && !empty($blacklistedWebsites)) {
            $blacklistedWebsites = array_map('trim', explode(',', $blacklistedWebsites));
        } else {
            $blacklistedWebsites = [];
        }

        foreach ($blacklistedProductIds as $productId) {
            try {
                // Načítame produkt na základe jeho ID
                $product = $this->productRepository->getById($productId);
                $assignedWebsiteIds = $product->getWebsiteIds();

                foreach ($this->storeManager->getWebsites() as $website) {
                    if (in_array($website->getCode(), $blacklistedWebsites)) {
                        if (in_array($website->getId(), $assignedWebsiteIds)) {
                            $newWebsiteIds = array_diff($assignedWebsiteIds, [$website->getId()]);
                            
                            // Logovanie pred uložením
                            $this->blacklistLogger->info("Product ID: {$productId}, New Websites after removal (array structure): " . var_export($newWebsiteIds, true));
                            
                            // Kontrola, či $newWebsiteIds obsahuje zmeny
                            if ($newWebsiteIds !== $assignedWebsiteIds) {
                                // Uistíme sa, že ide o sekvenčné pole integerov
                                $newWebsiteIds = array_map('intval', array_values($newWebsiteIds));
                                $product->setWebsiteIds($newWebsiteIds);
                                $this->productRepository->save($product);
                                $this->blacklistLogger->info("Product ID: {$productId} successfully updated.");
                            } else {
                                $this->blacklistLogger->info("Product ID: {$productId} is already removed from website: {$website->getCode()}.");
                            }
                        } else {
                            $this->blacklistLogger->info("Product ID: {$productId} is not assigned to website: {$website->getCode()}.");
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->blacklistLogger->error("Error updating product ID: {$productId} - " . $e->getMessage());
            }
        }
        
        $this->blacklistLogger->info("BlacklistCheck script completed successfully.");
        return $this;
    }
}
