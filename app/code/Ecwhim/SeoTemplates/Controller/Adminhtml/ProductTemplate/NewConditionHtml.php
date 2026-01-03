<?php
/**
 * Copyright © Ecwhim. All rights reserved.
 */

declare(strict_types=1);

namespace Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Ecwhim\SeoTemplates\Controller\Adminhtml\ProductTemplate;

class NewConditionHtml extends ProductTemplate implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id            = $this->getRequest()->getParam('id');
        $formName      = $this->getRequest()->getParam('form_namespace');
        $typeArr       = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type          = $typeArr[0];
        $templateModel = $this->_objectManager->create(\Ecwhim\SeoTemplates\Model\ProductTemplate::class);
        $model         = $this->_objectManager->create($type);
        $model
            ->setId($id)
            ->setType($type)
            ->setRule($templateModel)
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setFormName($formName);
            $model->setJsFormObject($this->getRequest()->getParam('form'));

            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        return $this->getResponse()->setBody($html);
    }
}
