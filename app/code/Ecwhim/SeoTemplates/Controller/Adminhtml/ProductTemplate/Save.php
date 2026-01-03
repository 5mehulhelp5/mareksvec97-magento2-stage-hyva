<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterface;

class Save extends \Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate implements HttpPostActionInterface
{
    /**
     * @var \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterfaceFactory
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
     * @param \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository
     * @param \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterfaceFactory $templateFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ecwhim\SeoTemplates\Api\ProductTemplateRepositoryInterface $templateRepository,
        \Ecwhim\SeoTemplates\Api\Data\ProductTemplateInterfaceFactory $templateFactory,
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

                $templateId = $this->getRequest()->getParam(ProductTemplateInterface::TEMPLATE_ID);

                /** @var \Ecwhim\SeoTemplates\Model\ProductTemplate $template */
                if (empty($templateId)) {
                    $template = $this->templateFactory->create();
                } else {
                    $template = $this->templateRepository->getById((int)$templateId);
                }

                $template->loadPost($data);

                $this->templateRepository->save($template);
                $this->messageManager->addSuccessMessage(
                    __('The template "%1" has been saved.', $template->getName())
                );
                $this->dataPersistor->clear(
                    \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface::ENTITY_TYPE_PRODUCT_TEMPLATE
                );

                if ($this->getRequest()->getParam('back')) {
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/edit',
                        [ProductTemplateInterface::TEMPLATE_ID => $template->getTemplateId()]
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
                \Ecwhim\SeoTemplates\Api\ProductTemplateManagementInterface::ENTITY_TYPE_PRODUCT_TEMPLATE,
                $data
            );

            $redirectPath   = empty($templateId) ? '*/*/new' : '*/*/edit';
            $redirectParams = empty($templateId) ? [] : [ProductTemplateInterface::TEMPLATE_ID => $templateId];

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
        if (empty($data[ProductTemplateInterface::NAME])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Name is missing. Enter and try again.')
            );
        }

        if (empty($data[ProductTemplateInterface::SCOPE])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Please choose Scope and try again.')
            );
        }

        if ($data[ProductTemplateInterface::SCOPE] === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_STORE
            && empty($data[ProductTemplateInterface::STORE_IDS])
        ) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Please choose Store View and try again.')
            );
        }

        if (empty($data[ProductTemplateInterface::TYPE])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Please choose Type and try again.')
            );
        }

        if (empty($data[ProductTemplateInterface::CONTENT])) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('The Content is missing. Enter and try again.')
            );
        }

        return true;
    }

    /**
     * @param array $data
     */
    protected function prepareData(array &$data): void
    {
        if ($data[ProductTemplateInterface::SCOPE] === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_GLOBAL) {
            $data[ProductTemplateInterface::STORE_IDS] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } elseif ($data[ProductTemplateInterface::SCOPE] === \Ecwhim\SeoTemplates\Model\Source\Scope::SCOPE_STORE
            && count($data[ProductTemplateInterface::STORE_IDS]) > 1
            && in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $data[ProductTemplateInterface::STORE_IDS])
        ) {
            $data[ProductTemplateInterface::STORE_IDS] = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        if (isset($data['rule'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
        }

        unset($data[ProductTemplateInterface::TEMPLATE_ID]);
        unset($data[\Ecwhim\SeoTemplates\Setup\Patch\Schema\AddProductTemplateTable::COLUMN_CONDITIONS_SERIALIZED]);
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
