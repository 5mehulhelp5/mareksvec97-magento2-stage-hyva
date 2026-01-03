<?php
namespace BigConnect\FmePricecalcPatch\Block\Product;

use Magento\Catalog\Block\Product\View as BaseView;
use Magento\Framework\Registry;

class Pricecalculator extends BaseView
{
    public $urlBuilder;
    public $storeManager;
    public $pricecalculatorHelper;
    protected $_fileDriver;
    protected $pcModel;
    private $_pcLoadedData = null;
    protected $_taxHelper;
    protected $_meta;
    private Registry $registry;

    // VOLITEĽNÉ: aby sa blok nerenderoval cez ESI s iným produktom
    // protected $_isScopePrivate = true;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \FME\Pricecalculator\Helper\Data $pricecalculatorData,
        \FME\Pricecalculator\Model\Pricecalculator $pcModel,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\App\ProductMetadataInterface $meta,
        \Magento\Catalog\Helper\Data $taxHelper,
        Registry $registry,
        array $data = []
    ) {
        $this->urlBuilder           = $context->getUrlBuilder();
        $this->storeManager         = $context->getStoreManager();
        $this->pricecalculatorHelper= $pricecalculatorData;
        $this->pcModel              = $pcModel;
        $this->_meta                = $meta;
        $this->_fileDriver          = $fileDriver;
        $this->_taxHelper           = $taxHelper;
        $this->registry             = $registry;

        parent::__construct(
            $context, $urlEncoder, $jsonEncoder, $string, $productHelper,
            $productTypeConfig, $localeFormat, $customerSession,
            $productRepository, $priceCurrency, $data
        );
    }

    /**
     * Dôležité: NEPOUŽÍVAME pôvodnú logiku v _construct(), lebo tam padalo na getId().
     * Ponecháme iba parent inicializáciu.
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Pripravíme všetko bezpečne až pred renderom – produkt už býva v registry.
     */
    protected function _beforeToHtml()
    {
        if (!$this->pricecalculatorHelper->isEnabledInFrontend()) {
            $this->setTemplate(null);
            return parent::_beforeToHtml();
        }

        $product = $this->getProduct() ?: $this->registry->registry('current_product');
        if (!$product || !$product->getId()) {
            // Žiadny pád – len nič nevyrenderuj
            $this->setTemplate(null);
            return parent::_beforeToHtml();
        }

        $pcData = $this->pcModel->getPcData((int)$product->getId());
        if ($pcData && (int)$pcData->getPcEnable() === 1) {
            $this->_pcLoadedData = $pcData;

            // ak si máš zapnuté rozlíšenie Hyvä/Luma v helperi, zachováme to
            if ($this->pricecalculatorHelper->isHyvaEnabledInFrontend()) {
                $this->setTemplate('FME_HyvaPricecalculator::pricecalculator.phtml');
            } else {
                $this->setTemplate('FME_Pricecalculator::pricecalculator.phtml');
            }
        } else {
            $this->setTemplate(null);
        }

        return parent::_beforeToHtml();
    }

    public function getProductPricingRule($pcLoadedData)
    {
        $pricingRule = [];
        $data = [
            'discount=' . $pcLoadedData->getPcDiscountMin() . ',' . $pcLoadedData->getPcDiscountMax(),
            'size='     . $pcLoadedData->getPcSizeMin()     . ',' . $pcLoadedData->getPcSizeMax(),
            strtolower($pcLoadedData->getPcMeasureBy()),
            strtolower($pcLoadedData->getPcDiscountType())
        ];

        foreach ($data as $item) {
            preg_match_all("/ ([^=]+) = ([^\\s]+) /x", $item, $p);
            $pair = array_combine($p[1], $p[2]);
            if (isset($pair['discount'])) {
                $pricingRule['discount'] = array_combine(['min_limit','max_limit'], explode(',', $pair['discount']));
            }
            if (isset($pair['size'])) {
                $pricingRule['size'] = array_combine(['min_limit','max_limit'], explode(',', $pair['size']));
            }
        }

        if (in_array('area', $data, true))   $pricingRule['by']   = 'area';
        if (in_array('volume', $data, true)) $pricingRule['by']   = 'volume';
        if (in_array('percentage', $data, true)) $pricingRule['type'] = 'percent';
        if (in_array('fixed', $data, true))      $pricingRule['type'] = 'fixed';

        return $pricingRule;
    }

    public function getMagentoVersion()
    {
        return $this->_meta->getVersion();
    }

    public function getPcLoadedData()
    {
        return $this->_pcLoadedData ?: false;
    }

    public function getPriceIncTax()
    {
        $product = $this->getProduct() ?: $this->registry->registry('current_product');
        if (!$product || !$product->getId()) {
            return null;
        }
        return $this->_taxHelper->getTaxPrice($product, $product->getFinalPrice(), true);
    }
}
