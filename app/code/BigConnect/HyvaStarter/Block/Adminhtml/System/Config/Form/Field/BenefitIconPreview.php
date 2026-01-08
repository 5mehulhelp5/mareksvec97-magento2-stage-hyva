<?php

declare(strict_types=1);

namespace BigConnect\HyvaStarter\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class BenefitIconPreview extends Field
{
    protected function _getElementHtml(AbstractElement $element): string
    {
        $elementHtml = parent::_getElementHtml($element);
        $previewId = $element->getHtmlId() . '_preview';
        $iconMap = $this->getIconSvgMap();
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

    private function getIconSvgMap(): array
    {
        return [
            'shield' => $this->wrapSvg('<path d="M12 3l7 4v5c0 5-3.5 9-7 10-3.5-1-7-5-7-10V7l7-4z"/><path d="M9 12l2 2 4-4"/>'),
            'globe' => $this->wrapSvg('<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3c3 3.5 3 14 0 18"/><path d="M12 3c-3 3.5-3 14 0 18"/>'),
            'badge' => $this->wrapSvg('<circle cx="12" cy="12" r="9"/><path d="M8.5 12l2.5 2.5L15.5 10"/>'),
            'thumb' => $this->wrapSvg('<path d="M7 10h2v9H7z"/><path d="M9 19h6.5a2.5 2.5 0 0 0 2.4-3.2l-1.3-4.3a2 2 0 0 0-1.9-1.4H12V6a2 2 0 0 0-2-2l-1 6H7"/>'),
            'phone' => $this->wrapSvg('<path d="M4.5 3.5h5l1 4-2 1a10 10 0 0 0 5 5l1-2 4 1v5c0 .8-.7 1.5-1.5 1.5C9.6 19 5 14.4 5 6.5 5 4.7 5.7 3.5 4.5 3.5z"/>'),
            'fallback' => $this->wrapSvg('<circle cx="12" cy="12" r="9"/><path d="M8.5 12l2.5 2.5L15.5 10"/>'),
        ];
    }

    private function wrapSvg(string $paths): string
    {
        return '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor"'
            . ' stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"'
            . ' style="display:block;">'
            . $paths
            . '</svg>';
    }
}
