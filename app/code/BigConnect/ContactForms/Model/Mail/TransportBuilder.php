<?php
namespace BigConnect\ContactForms\Model\Mail;

// use Magento\Framework\App\TemplateTypesInterface;
// use Magento\Framework\Exception\LocalizedException;
// use Magento\Framework\Exception\MailException;
// use Magento\Framework\Mail\EmailMessageInterface;
// use Magento\Framework\Mail\EmailMessageInterfaceFactory;
// use Magento\Framework\Mail\AddressConverter;
// use Magento\Framework\Mail\Exception\InvalidArgumentException;
// use Magento\Framework\Mail\MessageInterface;
// use Magento\Framework\Mail\MessageInterfaceFactory;
// use Magento\Framework\Mail\MimeInterface;
// use Magento\Framework\Mail\MimeMessageInterfaceFactory;
// use Magento\Framework\Mail\MimePartInterfaceFactory;
// use Magento\Framework\Mail\Template\FactoryInterface;
// use Magento\Framework\Mail\Template\SenderResolverInterface;
// use Magento\Framework\Mail\TemplateInterface;
// use Magento\Framework\Mail\TransportInterface;
// use Magento\Framework\Mail\TransportInterfaceFactory;
// use Magento\Framework\ObjectManagerInterface;
// use Magento\Framework\Filesystem;
// use Magento\Framework\Phrase;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zend\Mime\Mime;
// use Zend\Mime\PartFactory;

class TransportBuilder extends \Magecomp\Quickcontact\Model\Mail\TransportBuilder {
	
    // protected $templateIdentifier;
    // protected $templateModel;
    // protected $templateVars;
    // protected $templateOptions;
    // protected $transport;
    // protected $templateFactory;
    // protected $objectManager;
    // protected $message;
    // protected $_senderResolver;
    // protected $mailTransportFactory;
    // private $messageData = [];
    // private $emailMessageInterfaceFactory;
    // private $mimeMessageInterfaceFactory;
    // private $mimePartInterfaceFactory;
    // private $addressConverter;
    // protected $attachments = [];
    // protected $partFactory;
    // protected $filesystem;

    // public function __construct(
        // FactoryInterface $templateFactory,
        // MessageInterface $message,
        // SenderResolverInterface $senderResolver,
        // ObjectManagerInterface $objectManager,
        // TransportInterfaceFactory $mailTransportFactory,
        // MessageInterfaceFactory $messageFactory = null,
        // Filesystem $filesystem,
        // EmailMessageInterfaceFactory $emailMessageInterfaceFactory = null,
        // MimeMessageInterfaceFactory $mimeMessageInterfaceFactory = null,
        // MimePartInterfaceFactory $mimePartInterfaceFactory = null,
        // AddressConverter $addressConverter = null
    // ) {
        // $this->templateFactory = $templateFactory;
        // $this->objectManager = $objectManager;
        // $this->_senderResolver = $senderResolver;
        // $this->filesystem = $filesystem;
        // $this->mailTransportFactory = $mailTransportFactory;
        // $this->emailMessageInterfaceFactory = $emailMessageInterfaceFactory ?: $this->objectManager
            // ->get(EmailMessageInterfaceFactory::class);
        // $this->mimeMessageInterfaceFactory = $mimeMessageInterfaceFactory ?: $this->objectManager
            // ->get(MimeMessageInterfaceFactory::class);
        // $this->mimePartInterfaceFactory = $mimePartInterfaceFactory ?: $this->objectManager
            // ->get(MimePartInterfaceFactory::class);
        // $this->addressConverter = $addressConverter ?: $this->objectManager
            // ->get(AddressConverter::class);
        // $this->partFactory = $objectManager->get(PartFactory::class);
        // parent::__construct($templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory, $messageFactory, $emailMessageInterfaceFactory, $mimeMessageInterfaceFactory,
            // $mimePartInterfaceFactory, $addressConverter);
    // }

    public function addAttachment($attachments) {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
		$destinationPath = $mediaDirectory->getAbsolutePath('magecomp/quickcontact/');
        $attachmentPart = $this->partFactory->create();
		
        $i=0;
        foreach($attachments as $attachment) {
			if (!$attachment) continue;
			
            $attachmentPart = $this->partFactory->create();
			
            $attachmentPart->setContent(file_get_contents($destinationPath.$attachment))
                ->setType('application/pdf')
                ->setFileName($attachment)
                ->setDisposition(Mime::DISPOSITION_ATTACHMENT)
                ->setEncoding(Mime::ENCODING_BASE64);
                $this->attachments[$i] = $attachmentPart;
                $i++;
        }     
        return $this;
    }
}