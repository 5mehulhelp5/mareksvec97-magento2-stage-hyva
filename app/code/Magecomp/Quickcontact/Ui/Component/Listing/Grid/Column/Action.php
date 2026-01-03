<?php
namespace Magecomp\Quickcontact\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Action extends Column
{
    protected $_urlBuilder;

    private $_viewUrl;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $viewUrl = 'quickcontact/quickcontact/view',
        $deleteUrl = 'quickcontact/quickcontact/delete'
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_viewUrl = $viewUrl;
        $this->_deleteUrl = $deleteUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
    	if (isset($dataSource['data']['items'])) {
        	foreach ($dataSource['data']['items'] as &$item) {
            	$name = $this->getData('name');
                if (isset($item['id'])) {
                	$item[$name]['view'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_viewUrl,
                            ['id' => $item['id']]
                        ),
                        'label' => __('View'),
                    ];

                     $item[$name]['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            $this->_deleteUrl,
                            ['id' => $item['id']]
                        ),
                        'label' => __('Delete'),
                        'confirm'=> [
                            'title' => __('Delete'),
                            'message' => __('Are you sure you want to delete a this record?'),
                            ],
                    'hidden' => false,
                    ];
				}
            }
        }

        return $dataSource;
    }
}
