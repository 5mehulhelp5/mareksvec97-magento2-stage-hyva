<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

abstract class AbstractDeleteButton implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * AbstractDeleteButton constructor.
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request    = $request;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        if ($this->getTemplateId()) {
            return [
                'label'      => __('Delete'),
                'on_click'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?')
                    . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'class'      => 'delete',
                'sort_order' => 20
            ];
        }

        return [];
    }

    /**
     * @return int|null
     */
    abstract protected function getTemplateId(): ?int;

    /**
     * @return string
     */
    abstract protected function getDeleteUrl(): string;
}
