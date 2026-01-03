<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\CategoryTemplate;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterface;
use Ecwhim\SeoTemplates\Ui\DataProvider\CategoryTemplate\Form\Modifier\General as FormModifierGeneral;

class Save extends \Ecwhim\SeoTemplates\Controller\Adminhtml\CategoryTemplate implements HttpPostActionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterfaceFactory
     */
    protected $templateFactory;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Save constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository
     * @param \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterfaceFactory $templateFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecwhim\SeoTemplates\Api\CategoryTemplateRepositoryInterface $templateRepository,
        \Ecwhim\SeoTemplates\Api\Data\CategoryTemplateInterfaceFactory $templateFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);

        $this->templateRepository = $templateRepository;
        $this->templateFactory    = $templateFactory;
        $this->dataPersistor      = $dataPersistor;
        $this->logger             = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                $this->validateRequest();
                $this->validateData($data);
                $this->prepareData($data);

                $templateId = $this->getRequest()->getParam(CategoryTemplateInterface::TEMPLATE_ID);

                /** @var \Ecwhim\SeoTemplates\Model\CategoryTemplate $template */
                if (empty($templateId)) {
                    $template = $this->templateFactory->create();
                } else {
                    $template = $this->templateRepository->getById((int)$templateId);
                }

                $template->addData($data);
                $this->templateRepository->save($template);
                $this->messageManager->addSuccessMessage(
                    __('The template "%1" has been saved.', $template->getName())
                );
                $this->dataPersistor->clear(
                    \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface::ENTITY_TYPE_CATEGORY_TEMPLATE
                );

                if ($this->getRequest()->getParam('back')) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/edit',
                        [CategoryTemplateInterface::TEMPLATE_ID => $template->getTemplateId()]
                    );
                }

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            } catch (\Magento\Framework\Exception\NotFoundException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            } catch (\Magento\Framework\Exception\ValidatorException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the template.'));
                $this->logger->critical($e);
            }

            $this->dataPersistor->set(
                \Ecwhim\SeoTemplates\Api\CategoryTemplateManagementInterface::ENTITY_TYPE_CATEGORY_TEMPLATE,
                $data
            );

            $redirectPath   = empty($templateId) ? '*/*/new' : '*/*/edit';
            $redirectParams = empty($templateId) ? [] : [CategoryTemplateInterface::TEMPLATE_ID => $templateId];

            return $this->resultRedirectFactory->create()->setPath($redirectPath, $redirectParams);
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    protected function validateData(array $data): bool
    {
        if (empty($data[CategoryTemplateInterface::NAME])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Name is missing. Enter and try again.')
            );
        }

        if (empty($data[CategoryTemplateInterface::SCOPE])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Please choose Scope and try again.')
            );
        }

        if ($data[CategoryTemplateInterface::SCOPE] === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_STORE
            && empty($data[CategoryTemplateInterface::STORE_IDS])
        ) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Please choose Store View and try again.')
            );
        }

        if (empty($data[CategoryTemplateInterface::TYPE])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Please choose Type and try again.')
            );
        }

        if (empty($data[CategoryTemplateInterface::CONTENT])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Content is missing. Enter and try again.')
            );
        }

        if (empty($data[CategoryTemplateInterface::APPLY_TO_ALL_CATEGORIES])
            && empty($data[FormModifierGeneral::FIELD_CATEGORY_IDS])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Please choose Categories and try again.')
            );
        }

        return true;
    }

    /**
     * @param array $data
     */
    protected function prepareData(array &$data): void
    {
        if ($data[CategoryTemplateInterface::SCOPE] === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_GLOBAL) {
            $data[CategoryTemplateInterface::STORE_IDS] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } elseif ($data[CategoryTemplateInterface::SCOPE] === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_STORE
            && count($data[CategoryTemplateInterface::STORE_IDS]) > 1
            && in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $data[CategoryTemplateInterface::STORE_IDS])
        ) {
            $data[CategoryTemplateInterface::STORE_IDS] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        if (!empty($data[CategoryTemplateInterface::APPLY_TO_ALL_CATEGORIES])) {
            unset($data[FormModifierGeneral::FIELD_CATEGORY_IDS]);
        }

        unset($data[CategoryTemplateInterface::TEMPLATE_ID]);
        unset($data['form_key']);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    protected function validateRequest(): bool
    {
        if (!$this->getRequest()->isPost()) {
            throw new \Magento\Framework\Exception\NotFoundException(__('Page Not Found.'));
        }

        return true;
    }
}
