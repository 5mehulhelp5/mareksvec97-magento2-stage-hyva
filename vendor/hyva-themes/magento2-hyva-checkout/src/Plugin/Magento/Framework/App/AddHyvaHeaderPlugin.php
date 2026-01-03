<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Checkout\Plugin\Magento\Framework\App;

use Magento\Framework\App\FrontControllerInterface as CoreFrontControllerInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class AddHyvaHeaderPlugin
{
    private HttpRequest $request;

    public function __construct(
        HttpRequest $request
    ) {
        $this->request = $request;
    }

    /**
     * @param ResponseInterface|ResultInterface $result
     * @return ResponseHttp|ResultInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(CoreFrontControllerInterface $subject, $result)
    {
        if ($this->request->getRouteName() === 'hyva_checkout'
            && ($result instanceof ResultInterface || $result instanceof HttpInterface)
        ) {
            // This would be "Hyvä Checkout", if utf-8 would be supported...
            $result->setHeader('x-built-with', 'Hyva Checkout');
        }

        return $result;
    }
}
