<?php
namespace BigConnect\Inspiration\Block\Adminhtml\Inspiration\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    private Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getId(): ?int
    {
        return (int)$this->context->getRequest()->getParam('entity_id');
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
