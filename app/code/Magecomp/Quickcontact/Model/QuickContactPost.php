<?php

namespace Magecomp\Quickcontact\Model;

use Magecomp\Quickcontact\Api\QuickContactInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magecomp\Quickcontact\Helper\Data;
use Magecomp\Quickcontact\Model\Mail\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magecomp\Quickcontact\Model\QuickcontactFactory;
use Magento\Framework\Filesystem;


class QuickContactPost implements QuickContactInterface
{
    protected $QuickcontactFactory;
    protected $filesystem;
    protected $helperdata;
    protected $transportBuilder;
    protected $senderResolver;
    protected $state;
    protected $inlineTranslation;

    public function __construct(
        QuickcontactFactory $QuickcontactFactory,
        Filesystem $filesystem,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        Data $helperdata,
        SenderResolverInterface $senderResolver
    )
    {
        $this->filesystem = $filesystem;
        $this->QuickcontactFactory = $QuickcontactFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $state;
        $this->helperdata = $helperdata;
        $this->senderResolver = $senderResolver;
    }
    public function getConfigData($storeId)
    {
        try {
            if ($this->helperdata->getConfig('quickcontact/general/enable',$storeId)) {
            // if ($this->helper->isModuleEnabled($storeid)) {
                $data = [
                    "status" => true,
                    "Enable" => $this->helperdata->getConfig('quickcontact/general/enable',$storeId),
                    "Email ID of Admin" => $this->helperdata->getConfig('quickcontact/general/adminmailreceiver',$storeId),
                    "Email Template" => $this->helperdata->getConfig('quickcontact/general/email_template',$storeId),
                    "Email Sender" => $this->helperdata->getConfig('quickcontact/general/emailsender',$storeId),
                    "Email Subject" => $this->helperdata->getConfig('quickcontact/general/emailsubject',$storeId),
                    "Background Color" => $this->helperdata->getConfig('quickcontact/general/bgcolor',$storeId),
                ];
            } else {
                $data = ["status" => false, "errormessage" => __("Please Enable The Extension")];
            }
        }catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
        return json_encode($data);
    }
    public function setFormData($customername,$customeremail,$comment,$storeId,$image1,$image1_name,$image2,$image2_name,$image3,$image3_name,$image4,$image4_name,$image5,$image5_name)
    {
        $quickcontact=$this->QuickcontactFactory->create();
        $attachments[]=array();
        $i=0;

        try 
        {
            if ($this->helperdata->getConfig('quickcontact/general/enable',$storeId)) {
            $mediaDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            if($image1!=='' && $image1_name!=='')
            {
                $image_data1 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image1));
                $image_path1 = 'magecomp/quickcontact/' . $image1_name; //the path to the image file
                $mediaDirectory->writeFile($image_path1, $image_data1);
                $attachments[$i]=$image1_name;
                $i++;

            }

            if($image2!=='' && $image2_name!=='')
            {
                $image_data2 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image2));
                $image_path2 = 'magecomp/quickcontact/' . $image2_name; //the path to the image file
                $mediaDirectory->writeFile($image_path2, $image_data2);
                $attachments[$i]=$image2_name;
                $i++;
            }

            if($image3!=='' && $image3_name!=='')
            {
                $image_data3 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image3));
                $image_path3 = 'magecomp/quickcontact/' . $image3_name; //the path to the image file
                $mediaDirectory->writeFile($image_path3, $image_data3);
                $attachments[$i]=$image3_name;
                $i++;
            }

            if($image4!=='' && $image4_name!=='')
            {
                $image_data4 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image4));
                $image_path4 = 'magecomp/quickcontact/' . $image4_name; //the path to the image file
                $mediaDirectory->writeFile($image_path4, $image_data4);
                $attachments[$i]=$image4_name;
                $i++;
            }

            if($image5!=='' && $image5_name!=='')
            {
                $image_data5 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image5));
                $image_path5 = 'magecomp/quickcontact/' . $image5_name; //the path to the image file
                $mediaDirectory->writeFile($image_path5, $image_data5);
                $attachments[$i]=$image5_name;
            }

            $attachment=implode(',', $attachments);

            $quickcontact->setCustomername($customername);
            $quickcontact->setCustomeremail($customeremail);
            $quickcontact->setComment($comment);
            $quickcontact->setAttachment($attachment);
            $datasave = $quickcontact->save();
            
            //send email

             if($datasave)
             {
                $templateId = $this->helperdata->getConfig('quickcontact/general/email_template'); // template id
                $fromEmail = $this->senderResolver->resolve($this->helperdata->getConfig('quickcontact/general/emailsender'));  // sender Email id
                $toEmail = $this->helperdata->getConfig('quickcontact/general/adminmailreceiver'); // receiver email id
                $subject = $this->helperdata->getConfig('quickcontact/general/emailsubject'); // receiver email id
                try {
                    // template variables pass here
                    $templateVars = [
                        'subject' => $subject,
                        'customerName' => $customername,
                        'customerEmail' => $customeremail,
                        'customerComment' => $comment
                    ];
         
                    // $storeId = $this->storeManager->getStore()->getId();
                    $this->inlineTranslation->suspend();
         
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $templateOptions = [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId
                    ];
                    $attachments=explode(',', $attachment);
                    $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                        ->setTemplateOptions($templateOptions)
                        ->setTemplateVars($templateVars)
                        ->addAttachment($attachments)
                        ->setFrom($fromEmail)
                        ->addTo($toEmail)
                        ->getTransport();
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                } catch (\Exception $e) {
                   throw new AuthenticationException(__($e->getMessage()));
                }
             }
                
            $data = ["status" => true, "message" => __("Data Save Successfully")];
            } else {
                $data = ["status"=>false, "message"=> "Please Enable The Extension"];
            }
             return json_encode($data);
        }catch (\Exception $e) {
            throw new AuthenticationException(__($e->getMessage()));
        }
    }
   
}
