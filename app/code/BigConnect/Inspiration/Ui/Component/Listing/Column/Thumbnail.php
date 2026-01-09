<?php
namespace BigConnect\Inspiration\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    private StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            foreach ($dataSource['data']['items'] as & $item) {
                if (!empty($item['image'])) {
                    $url = $mediaBaseUrl . ltrim($item['image'], '/');
                    $item[$this->getData('name') . '_src'] = $url;
                    $item[$this->getData('name') . '_alt'] = $item['customer_name'] ?? __('Inspiration');
                    $item[$this->getData('name') . '_link'] = $this->context->getUrlBuilder()->getUrl(
                        'bigconnect_inspiration/inspiration/edit',
                        ['entity_id' => $item['entity_id']]
                    );
                }
            }
        }

        return $dataSource;
    }
}
