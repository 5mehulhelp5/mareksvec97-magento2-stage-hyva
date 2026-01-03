<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ReviewReminderBase\Ui\Component\Listing\Column;

use Exception;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use MageWorx\ReviewReminderBase\Model\Reminder\Source\Type;

class ReminderActions extends Column
{
    /**
     * Url path to edit email reminder
     *
     * @var string
     */
    const URL_PATH_EDIT_EMAIL = 'mageworx_reviewreminderbase/reminder/editEmail';

    /**
     * Url path to edit popup reminder
     *
     * @var string
     */
    const URL_PATH_EDIT_POPUP = 'mageworx_reviewreminderbase/reminder/editPopup';

    /**
     * Url path to delete
     *
     * @var string
     */
    const URL_PATH_DELETE = 'mageworx_reviewreminderbase/reminder/delete';

    /**
     * Url builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                if (isset($item['reminder_id'])) {
                    $item[$this->getData('name')] = [
                        'edit'   => [
                            'href'  => $this->urlBuilder->getUrl(
                                $this->getEditUrl($item['type']),
                                [
                                    'reminder_id' => $item['reminder_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href'    => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'reminder_id' => $item['reminder_id']
                                ]
                            ),
                            'label'   => __('Delete'),
                            'confirm' => [
                                'title'   => __('Delete record'),
                                'message' => __(
                                    'Are you sure you want to delete the Reminder "%1" ?',
                                    '${ $.$data.name }'
                                ),
                                '__disableTmpl' => ['message' => false]
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param string $type
     * @return string
     * @throws Exception
     */
    protected function getEditUrl($type): string
    {
        switch ($type) {
            case Type::TYPE_EMAIL:
                $url = self::URL_PATH_EDIT_EMAIL;
                break;
            case Type::TYPE_POPUP:
                $url = self::URL_PATH_EDIT_POPUP;
                break;
            default:
                throw new Exception('Unknown Review Reminder Type');
                break;
        }

        return $url;
    }
}
