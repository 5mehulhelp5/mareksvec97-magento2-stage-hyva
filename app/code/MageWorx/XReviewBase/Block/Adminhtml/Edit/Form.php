<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XReviewBase\Block\Adminhtml\Edit;

use Magento\Catalog\Model\Product\Attribute\Repository as ProductAttributeRepository;
use Magento\Config\Model\Config\Source\Yesno;

/**
 * Adminhtml Review Edit Form
 */
class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{
    /**
     * @var Yesno
     */
    protected $yesnoOptions;

    /**
     * @var ProductAttributeRepository
     */
    protected $productAttributeRepository;

    /**
     * Form constructor.
     *
     * @param ProductAttributeRepository $productAttributeRepository
     * @param Yesno $yesnoOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Review\Helper\Data $reviewData
     * @param array $data
     */
    public function __construct(
        ProductAttributeRepository $productAttributeRepository,
        \Magento\Config\Model\Config\Source\Yesno $yesnoOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Review\Helper\Data $reviewData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $systemStore,
            $customerRepository,
            $productFactory,
            $reviewData,
            $data
        );

        $this->yesnoOptions               = $yesnoOptions;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * Prepare edit review form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form = $this->getForm();
        $form->setDataObject($this->getReview());

        $originalFieldset = $form->getElement('review_details');

        $originalFieldset->addField(
            'pros',
            'textarea',
            [
                'label' => __('Pros'),
                'name'  => 'pros',
                'style' => 'height:18em;',
                'after_element_html' => '<script>require(["prototype"], function () {});</script>'
            ],
            'detail'
        );

        $originalFieldset->addField(
            'cons',
            'textarea',
            [
                'label' => __('Cons'),
                'name'  => 'cons',
                'style' => 'height:18em;',
            ],
            'pros'
        );

        $originalFieldset->addField(
            'answer',
            'textarea',
            [
                'label' => __("Vendor's Answer"),
                'name'  => 'answer',
                'style' => 'height:18em;',
            ],
            'cons'
        );

        $fieldset = $form->addFieldset(
            'mageworx-xreviewbase-additional',
            ['legend' => __('Additional Fields'), 'class' => 'fieldset-wide', ]
        );

        $fieldset->addField(
            'location',
            'text',
            [
                'label' => __('Country Code'),
                'name'  => 'location'
            ]
        );

        $fieldset->addField(
            'region',
            'text',
            [
                'label' => __('Region'),
                'name'  => 'region'
            ]
        );

        $fieldset->addField(
            'is_recommend',
            'select',
            [
                'name'    => 'is_recommend',
                'label'   => __('Is Recommend'),
                'title'   => __('Is Recommend'),
                'options' => $this->yesnoOptions->toArray(),
            ]
        );

        $fieldset->addField(
            'is_verified',
            'select',
            [
                'name'    => 'is_verified',
                'label'   => __('Is Verified'),
                'title'   => __('Is Verified'),
                'options' => $this->yesnoOptions->toArray()
            ]
        );

        // We use product attribute as workaround for building media gallery fieldset. Data are used from review.
        try {
            $attribute = $this->productAttributeRepository->get('media_gallery');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $attribute = false;
        }

        if ($attribute) {
            $fieldset = $form->addFieldset(
                'mageworx-xreviewbase-media',
                [
                    'class' => 'user-defined',
                    'legend' => __('Media Gallery'),
                    'collapsable' => false
                ]
            );

            $this->_setFieldset([$attribute], $fieldset);
        }

        $form->setValues($this->getReview()->getData());

        return $this;
    }

    /**
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array_merge(
            parent::_getAdditionalElementTypes(),
            ['gallery' => 'MageWorx\XReviewBase\Block\Adminhtml\Helper\Form\Gallery']
        );
    }

    /**
     * Retrieve review object from registry
     *
     * @return \Magento\Review\Model\Review
     */
    public function getReview()
    {
        return $this->_coreRegistry->registry('review_data');
    }
}
