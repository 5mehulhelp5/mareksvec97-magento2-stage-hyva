<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Block\Adminhtml\ProductTemplate\Edit\Tab;

use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterfaceFactory
     */
    protected $templateFactory;

    /**
     * @var ProductTemplateInterface|null
     */
    protected $templateModel;

    /**
     * Conditions constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterfaceFactory $templateFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository,
        \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterfaceFactory $templateFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->conditions         = $conditions;
        $this->templateRepository = $templateRepository;
        $this->templateFactory    = $templateFactory;
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('Conditions')->__toString();
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('Conditions')->__toString();
    }

    /**
     * @inheritDoc
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $this->addTabToForm($form);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @param string $fieldsetId
     * @param string $formName
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function addTabToForm(
        \Magento\Framework\Data\Form $form,
        string $fieldsetId = 'conditions_fieldset',
        string $formName = 'ecwhim_seotemplates_product_template_form'
    ): void {
        /** @var \Ecwhim\SeoTemplates\Model\ProductTemplate $templateModel */
        $templateModel        = $this->getTemplateModel();
        $conditionsFieldSetId = $formName . '_' . $fieldsetId;

        if ($templateModel->getTemplateId()) {
            $conditionsFieldSetId .= '_' . $templateModel->getTemplateId();
        }

        $newChildUrl = $this->getUrl(
            'ecwhim_seotemplates/productTemplate/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $renderer = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Renderer\Fieldset::class);
        $renderer
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset(
            $fieldsetId,
            ['legend' => __('Conditions (don\'t add conditions if template is applied to all products)')]
        );
        $fieldset->setRenderer($renderer);

        $field = $fieldset->addField(
            'conditions',
            'text',
            [
                'name'           => 'conditions',
                'label'          => __('Conditions'),
                'title'          => __('Conditions'),
                'required'       => true,
                'data-form-part' => $formName
            ]
        );
        $field
            ->setRule($templateModel)
            ->setRenderer($this->conditions);

        $form->setValues($templateModel->getData());
        $this->setConditionFormName($templateModel->getConditions(), $formName, $conditionsFieldSetId);
    }

    /**
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormName
     */
    protected function setConditionFormName(
        \Magento\Rule\Model\Condition\AbstractCondition $conditions,
        string $formName,
        string $jsFormName
    ): void {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }

    /**
     * @return ProductTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getTemplateModel(): ProductTemplateInterface
    {
        if (isset($this->templateModel)) {
            return $this->templateModel;
        }

        $templateId = $this->getRequest()->getParam(ProductTemplateInterface::TEMPLATE_ID);

        if (empty($templateId)) {
            $this->templateModel = $this->templateFactory->create();
        } else {
            $this->templateModel = $this->templateRepository->getById((int)$templateId);
        }

        return $this->templateModel;
    }
}
