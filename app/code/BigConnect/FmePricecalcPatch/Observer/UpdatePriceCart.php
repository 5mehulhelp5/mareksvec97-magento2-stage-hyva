<?php
declare(strict_types=1);

namespace BigConnect\FmePricecalcPatch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use FME\Pricecalculator\Helper\Data as PcHelper;
use FME\Pricecalculator\Model\Pricecalculator as PcModel;

class UpdatePriceCart implements ObserverInterface
{
    public function __construct(
        private RequestInterface $request,
        private PcHelper $pcHelper,
        private PcModel $pcModel
    ) {}

    public function execute(Observer $observer)
    {
        if (!$this->pcHelper->isEnabledInFrontend()) {
            return;
        }

        $quoteItem = $observer->getEvent()->getQuoteItem();
        if (!$quoteItem) {
            return;
        }

        /**
         * VŽDY pracuj s PARENT quote itemom (configurable riadok),
         * child produkt vytiahneme cez option 'simple_product'.
         */
        $parentQuoteItem = $quoteItem->getParentItem() ?: $quoteItem;

        // z parent quote itemu skús vytiahnuť child produkt (vybraný materiál)
        $childProduct = null;
        $simpleOpt = $parentQuoteItem->getOptionByCode('simple_product');
        if ($simpleOpt && $simpleOpt->getProduct()) {
            $childProduct = $simpleOpt->getProduct();
        }

        $parentProduct = $parentQuoteItem->getProduct();

        // PC dáta z parenta (MUSIA byť zapnuté – berieme z nich konverzie a pravidlá)
        $pcParent = $this->pcModel->getPcData((int)$parentProduct->getId());
        if (!$pcParent || (int)$pcParent->getPcEnable() !== 1) {
            // ak parent nemá zapnutý kalkulátor, nerátame (modul sa neaplikuje)
            return;
        }

        // (Nepovinné) PC dáta z child – MÔŽU byť vypnuté; zaujíma nás hlavne unit price
        $pcChild = $childProduct ? $this->pcModel->getPcData((int)$childProduct->getId()) : null;

        /**
         * ZISTI POSTED OPTIONS (šírka, výška, …)
         * – primárne z requestu, inak z buyRequest parent quote itemu (funguje pri re-quote/reorder).
         */
        $params  = $this->request->getParams();
        $posted  = $params['options'] ?? [];
        if (!$posted && method_exists($parentQuoteItem, 'getBuyRequest') && $parentQuoteItem->getBuyRequest()) {
            $posted = (array) ($parentQuoteItem->getBuyRequest()->getData('options') ?? []);
        }

        /**
         * BASE CENA (bez DPH)
         * – pre configurable berieme z CHILD (vybraný materiál), inak z parent produktu.
         */
        $baseExcl = (float) ($childProduct ? $childProduct->getFinalPrice() : $parentProduct->getFinalPrice());

        /**
         * UNIT PRICE (€/m²)
         * – 1) ak má child PC vyplnenú > 0 → použijeme ju,
         * – 2) inak z parent PC,
         * – 3) inak fallback na base cenu.
         */
        $unitPrice = 0.0;
        if ($pcChild) {
            $rawChild = (string) $pcChild->getPcUnitPrice();
            $valChild = (float) str_replace(',', '.', $rawChild);
            if ($rawChild !== '' && $valChild > 0) {
                $unitPrice = $valChild;
            }
        }
        if ($unitPrice <= 0) {
            $rawParent = (string) $pcParent->getPcUnitPrice();
            $valParent = (float) str_replace(',', '.', $rawParent);
            $unitPrice = ($rawParent !== '' && $valParent > 0) ? $valParent : $baseExcl;
        }

        /**
         * PLOCHA/OBJEM – rozmery z parent custom options, konverzie z parent PC dát
         */
        $areaOut = $this->calculateAreaFromParent($parentProduct, $posted, $pcParent);

        /**
         * ZĽAVA – pravidlá z parent PC dát (rovnaké správanie ako pôvodný modul)
         */
        $discount = $this->calculateDiscount($areaOut, $unitPrice, $pcParent);

        /**
         * VÝSLEDOK (bez DPH): base + unit*area - discount
         */
        $deltaExcl   = ($unitPrice * $areaOut) - $discount;
        $updatePrice = $baseExcl + $deltaExcl;

        // Nastav cenu NA PARENT QUOTE ITEM (Magento štandard pri configurable)
        $parentQuoteItem->setCustomPrice($updatePrice);
        $parentQuoteItem->setOriginalCustomPrice($updatePrice);
        $parentQuoteItem->getProduct()->setIsSuperMode(true);
    }

    private function calculateAreaFromParent($parentProduct, array $postedOptions, $pcData): float
    {
        $fieldOptions = $this->pcHelper->getFieldOptions($pcData);
        if (!$fieldOptions) {
            return 1.0;
        }

        $area = 1.0;
        foreach ($parentProduct->getOptions() as $option) {
            if (isset($fieldOptions[$option->getTitle()])) {
                $val = isset($postedOptions[$option->getId()]) ? (float)$postedOptions[$option->getId()] : 0.0;
                $area *= $val;
            }
        }

        $measureBy  = $pcData->getPcMeasureBy();
        $inputUnit  = $pcData->getPcInputUnits();
        $outputUnit = $pcData->getPcOutputUnits();
        $unitConv   = (float) $this->pcHelper->unitConversion($inputUnit, $outputUnit, $measureBy);

        return $area * $unitConv;
    }

    private function calculateDiscount(float $area, float $unitPrice, $pcData): float
    {
        $rules = $this->pcHelper->getProductPricingRule($pcData);
        if (!$rules || !isset($rules['size'], $rules['discount'])) return 0.0;

        $minS = (float) ($rules['size']['min_limit']   ?? 0);
        $maxS = (float) ($rules['size']['max_limit']   ?? 0);
        $minD = (float) ($rules['discount']['min_limit'] ?? 0);
        $maxD = (float) ($rules['discount']['max_limit'] ?? 0);
        $isPercent = ($pcData->getPcDiscountType() === 'percentage');

        if ($area < $minS) return 0.0;

        if ($area < $maxS) {
            return $isPercent ? ($area * $unitPrice) * ($minD / 100.0) : $minD;
        }
        return $isPercent ? ($area * $unitPrice) * ($maxD / 100.0) : $maxD;
    }
}
