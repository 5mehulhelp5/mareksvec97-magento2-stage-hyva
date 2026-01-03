<?php
namespace Magecomp\Quickcontact\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Emailaction extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /** Url Path */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = array(),
        array $data = array()) 
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {              
                if (isset($item['customeremail'])) {                    
                    $item['customeremail'] = html_entity_decode('<a href="mailto:'.$item['customeremail'].'">'.$item['customeremail'].'</a>');
                }
            }
        }
        return $dataSource;
    }
}
