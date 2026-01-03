<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Observer\Adminhtml;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\App\Request\Http;

class LayoutHandler implements \Magento\Framework\Event\ObserverInterface
{
    /**
     *
     * @var Http
     */
    public $request;

    /**
     *
     * @var $paramName
     */
    public $paramName = 'section';

    /**
     *
     * @param Http $request
     */
    public function __construct(Http $request)
    {
        $this->request = $request;
    }

    /**
     * Add handles to the admin config section.
     *
     * @param Observer $observer
     * @event layout_load_before
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $params = $this->request->getParams();

        if (! empty($params['section'])) {
            $moduleName = $this->getModuleNameLc();
            if ($params['section'] === 'hyva_themes_checkout') {
                /** @var LayoutInterface $layout */
                $layout = $observer->getData('layout');
                $layout->getUpdate()->addHandle('adminhtml_system_config_edit_section_' . $moduleName);
            }
        }
    }

    /**
     * Get Module Name in lowercase
     *
     * @return string $moduleName
     */
    private function getModuleNameLc(): string
    {
        $class = get_class($this);
        $moduleName = strtolower(
            str_replace('\\', '_', substr($class, 0, strpos($class, '\\Observer')))
        );
        return (string) $moduleName;
    }
}
