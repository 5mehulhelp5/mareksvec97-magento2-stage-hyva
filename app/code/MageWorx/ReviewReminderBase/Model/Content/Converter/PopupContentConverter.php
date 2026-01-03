<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\ReviewReminderBase\Model\Content\Converter;

use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\Component\Product as ReminderProduct;
use MageWorx\ReviewReminderBase\Model\Content\DataContainer\PopupDataContainer;

class PopupContentConverter
{
    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Popup constructor.
     *
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * @param PopupDataContainer $reminderDataContainer
     * @return string
     */
    public function convert(PopupDataContainer $reminderDataContainer): ?string
    {
        if (empty($reminderDataContainer->getProductIds())) {
            return '';
        }

        $htmlTemplate = $this->addScriptForClosing($reminderDataContainer->getContent());

        $vars = $this->getVarsFromTemplate($htmlTemplate);
        if (empty($vars)) {
            return $htmlTemplate;
        }

        if (!preg_match('@((?s).*)(<products>(?s).*<\/products>)((?s).*)@miu', $htmlTemplate, $htmlParts)) {
            return $htmlTemplate;
        }

        if (!$htmlParts || count($htmlParts) < 2) {
            return $htmlTemplate;
        }

        array_shift($htmlParts);

        $htmlContent = '';

        foreach ($htmlParts as $key => $htmlPart) {

            if (mb_stripos($htmlPart, '<products>') !== false) {

                foreach ($reminderDataContainer->getOrderProducts() as $offerId => $offerData) {

                    /** @var ReminderProduct $product */
                    foreach ($offerData as $product) {
                        $htmlContent .= $this->replaceVariables($htmlParts[$key], $product);
                    }
                }
            } else {
                $htmlContent .= $this->replaceVariables($htmlParts[$key], $reminderDataContainer);
            }
        }

        return str_replace(['<products>', '</products>'], '', $htmlContent);
    }

    /**
     * @param string $string
     * @param DataObject $entity
     * @return string
     */
    protected function replaceVariables(string $string, DataObject $entity): string
    {
        $vars = $this->getVarsFromTemplate($string);

        foreach ($vars as $var) {
            $parts    = explode('.', $var);
            $property = (count($parts) > 1) ? $parts[1] : $parts[0];
            $value    = $entity->getDataUsingMethod($property);

            if (strpos($property, 'url') === false) {
                $value = $this->escaper->escapeHtmlAttr($value);
            }

            if ($value !== null) {
                $string = str_replace("[$var]", $value, $string);
            }
        }

        return $string;
    }

    /**
     * @param string $template
     * @return array
     */
    protected function getVarsFromTemplate(string $template): array
    {
        return $this->parse($template);
    }

    /**
     * @param string $string
     * @return array
     */
    protected function parse(string $string): array
    {
        $vars = [];

        if (preg_match_all('/\[(.*?)\]/', $string, $matches)) {
            $vars = $matches[1];
        }

        return $vars;
    }

    /**
     * @param string $html
     * @return string
     */
    protected function addScriptForClosing(string $html): string
    {
        return $html .
            "<script>
            (function () {
                var modal = document.querySelector('.mwrv-modal');
                var closeButton = modal.querySelector('.mwrv-modal__close');

                closeButton.addEventListener('click', function () {
                    modal.classList.remove('mwrv-modal--open');
                });
            })();
        </script>";
    }
}
