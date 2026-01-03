<?php

namespace Magecomp\Quickcontact\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magecomp\Quickcontact\Model\QuickcontactFactory;
use Magecomp\Quickcontact\Helper\Data;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magecomp\Quickcontact\Model\Mail\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Psr\Log\LoggerInterface;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $_jsonResultFactory;
    protected $_quickcontactFactory;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
    protected $helperdata;
    protected $senderResolver;
    protected $state;
    protected $logger;

    public function __construct(
        Context $context,
         UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem,
        Data $helperdata,
        SenderResolverInterface $senderResolver,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        LoggerInterface $logger,
        QuickcontactFactory $quickcontactFactory,
        JsonFactory $jsonResultFactory
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->_quickcontactFactory = $quickcontactFactory;
        $this->_jsonResultFactory = $jsonResultFactory;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->inlineTranslation = $state;
        $this->helperdata = $helperdata;
        $this->senderResolver = $senderResolver;
        parent::__construct($context);
    }
    public function execute()
    {
        try{
        $jsonResult = $this->_jsonResultFactory->create();
        $quickcontact=$this->_quickcontactFactory->create();
        $datafiles = $this->getRequest()->getFiles('attachment');
        $data = $this->getRequest()->getParams();
        $i=0;

        //save data and upload files in folder
        foreach($datafiles as $datafile){
        if(isset($datafile['name']) && $datafile['name'] != '') {
            try{
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => $datafiles[$i]]);
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png','txt','pdf','docx']);
                $imageAdapter = $this->adapterFactory->create();
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('magecomp/quickcontact');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                $imagePath = $result['file'];

                $data['files'][$i] = $imagePath;
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $jsonResult->setData(['success' => false,'message'=>__($e->getMessage())]);
                return $jsonResult;
            }
        }
        $i++;
        }
        if(isset($datafile['name']) && $datafile['name'] != ''){
         $data['attachment']=implode(',', $data['files']);
     }
         $datasave = $quickcontact->setData($data)->save();

         //send email

         if($datasave)
         {
            $templateId = $this->helperdata->getConfig('quickcontact/general/email_template'); // template id
        $fromEmail = $this->senderResolver->resolve($this->helperdata->getConfig('quickcontact/general/emailsender'));  

        // sender Email id
        $toEmail = $this->helperdata->getConfig('quickcontact/general/adminmailreceiver'); // receiver email id
        $subject = $this->helperdata->getConfig('quickcontact/general/emailsubject'); // receiver email id
        try {
            
            // template variables pass here
            $templateVars = [
                'subject' => $subject,
                'customerName' => $quickcontact->getCustomername(),
                'customerEmail' => $quickcontact->getCustomeremail(),
                'customerComment' => $quickcontact->getComment()
            ];
 
            $storeId = $this->storeManager->getStore()->getId();
            $this->inlineTranslation->suspend();
 
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $attachments='';
            if($quickcontact->getAttachment()){
            $attachments=explode(',', $quickcontact->getAttachment());
            }
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->addAttachment($attachments)
                ->setFrom($fromEmail)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
             $jsonResult->setData(['success' => true,'message'=>__("Success")]);
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $jsonResult->setData(['success' => false,'message'=>__($e->getMessage())]);
            return $jsonResult;
        }
         }

       }catch(\Exception $e){
        $this->logger->info($e->getMessage());
                $jsonResult->setData(['success' => false,'message'=>__($e->getMessage())]);
                return $jsonResult;
       }
        return $jsonResult;     
    }
}
