<?php
declare(strict_types=1);

namespace BigConnect\Inspiration\Ui\Component\Listing\Column;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    private StoreManagerInterface $storeManager;
    private BackendUrlInterface $backendUrl;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        BackendUrlInterface $backendUrl,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->backendUrl = $backendUrl;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        foreach ($dataSource['data']['items'] as &$item) {
            $image = $item['image'] ?? '';
            if (!$image) {
                continue;
            }

            // normalizácia hodnoty z DB
            $path = ltrim((string)$image, '/');

            // ak je v DB len "luca-min.jpg", doplň prefix podľa ImageUploader basePath (inspirations)
            if (strpos($path, '/') === false) {
                $path = 'inspirations/' . $path;
            }

            // výsledná URL na obrázok v pub/media
            $url = $mediaBaseUrl . $path;

            $name = (string)$this->getData('name');

            $item[$name . '_src']  = $url;
            $item[$name . '_orig_src'] = $url; // niekedy pomáha pre thumbnail komponent
            $item[$name . '_alt']  = $item['customer_name'] ?? (string)__('Inspiration');

            $item[$name . '_link'] = $this->backendUrl->getUrl(
                'bigconnect_inspiration/inspiration/edit',
                ['entity_id' => $item['entity_id'] ?? null]
            );
        }

        return $dataSource;
    }
}
