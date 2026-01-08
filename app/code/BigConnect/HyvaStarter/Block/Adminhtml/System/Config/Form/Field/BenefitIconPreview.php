<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Block\Adminhtml\System\Config\Form\Field;

use Hyva\Theme\ViewModel\HeroiconsOutline;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;

class BenefitIconPreview extends Field
{
    protected function _getElementHtml(AbstractElement $element): string
    {
        $elementHtml = parent::_getElementHtml($element);
        $previewId = $element->getHtmlId() . '_preview';
        $heroIcons = ObjectManager::getInstance()->get(HeroiconsOutline::class);
        $iconMap = $this->getIconSvgMap($heroIcons);
        $currentValue = (string) $element->getValue();
        $initialSvg = $iconMap[$currentValue] ?? $iconMap['fallback'];
        $iconMapJson = json_encode($iconMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        $previewHtml = '<div id="' . $this->escapeHtmlAttr($previewId) . '"'
            . ' class="hyvastarter-benefit-icon-preview" style="margin-top:8px;">'
            . $initialSvg
            . '</div>';

        $script = <<<HTML
<script>
(() => {
    const iconMap = {$iconMapJson};
    const select = document.getElementById('{$this->escapeHtmlAttr($element->getHtmlId())}');
    const preview = document.getElementById('{$this->escapeHtmlAttr($previewId)}');
    if (!select || !preview) {
        return;
    }
    const updatePreview = () => {
        const value = select.value || 'fallback';
        preview.innerHTML = iconMap[value] || iconMap.fallback || '';
    };
    select.addEventListener('change', updatePreview);
    updatePreview();
})();
</script>
HTML;

        return $elementHtml . $previewHtml . $script;
    }

    private function getIconSvgMap(HeroiconsOutline $heroIcons): array
    {
        return [
            'shield' => $heroIcons->shieldCheckHtml('w-6 h-6 text-primary'),
            'globe' => $heroIcons->globeAltHtml('w-6 h-6 text-primary'),
            'badge' => $heroIcons->badgeCheckHtml('w-6 h-6 text-primary'),
            'thumb' => $heroIcons->thumbUpHtml('w-6 h-6 text-primary'),
            'phone' => $heroIcons->phoneHtml('w-6 h-6 text-primary'),
            'fallback' => $heroIcons->badgeCheckHtml('w-6 h-6 text-primary'),
        ];
    }
}
