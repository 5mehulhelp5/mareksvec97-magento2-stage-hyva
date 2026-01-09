<?php
namespace BigConnect\Inspiration\Ui\DataProvider;

use BigConnect\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class FormDataProvider extends AbstractDataProvider
{
    private RequestInterface $request;
    private StoreManagerInterface $storeManager;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        $data = [];
        $id = (int)$this->request->getParam($this->requestFieldName);

        if ($id) {
            $item = $this->collection->getItemById($id);
            if ($item) {
                $data[$item->getId()] = $item->getData();

                if (!empty($data[$item->getId()]['image'])) {
                    $image = $data[$item->getId()]['image'];
                    $data[$item->getId()]['image'] = [[
                        'name' => basename($image),
                        'url' => $this->getMediaUrl($image),
                    ]];
                }
            }
        }

        return $data;
    }

    private function getMediaUrl(string $path): string
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . ltrim($path, '/');
    }
}
