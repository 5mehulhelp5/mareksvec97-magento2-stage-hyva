<?php
namespace Hyva\GeisswebEuvat\Block\VatNumber;

use Geissweb\Euvat\Api\Data\ValidationInterface;
use Geissweb\Euvat\Helper\Configuration;
use Geissweb\Euvat\Helper\VatNumber\Formatter;
use Geissweb\Euvat\Model\ValidationRepository;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Field extends Template
{
    public const FORM_ADDRESS_EDIT = 'address_edit';
    public const FORM_ACCOUNT_CREATE = 'account_create';
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;
    /**
     * @var \Geissweb\Euvat\Helper\VatNumber\Formatter
     */
    protected $formatter;
    /**
     * @var \Geissweb\Euvat\Model\ValidationRepository
     */
    private $validationRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Configuration $config
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Geissweb\Euvat\Helper\VatNumber\Formatter $formatter
     * @param \Geissweb\Euvat\Model\ValidationRepository $validationRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Configuration $config,
        Json $jsonSerializer,
        Formatter $formatter,
        ValidationRepository $validationRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
        $this->jsonSerializer = $jsonSerializer;
        $this->formatter = $formatter;
        $this->validationRepository = $validationRepository;

        $this->setTemplate('Geissweb_Euvat::vat-number-front.phtml');
    }

    public function getHyvaFieldConfig()
    {
        $config = $this->config->getVatFieldConfig();
        unset($config['template'], $config['elementTmpl'], $config['taxCalcMethod'], $config['customScope']);

        switch($this->getForm()) {
            case self::FORM_ADDRESS_EDIT:
                $validation = $this->config->getFieldValidationAtAddressEdit();
                break;
            case self::FORM_ACCOUNT_CREATE:
                $validation = $this->config->getFieldValidationAtRegistration();
                break;
            default:
                $validation = [];
                break;
        }

        $config['validation'] = $validation;
        return $this->jsonSerializer->serialize($config);
    }

    public function getValidationResult(): ?ValidationInterface
    {
        $validationResult = $this->validationRepository->getByVatId($this->getValue());
        if($validationResult instanceof ValidationInterface) {
            return $validationResult;
        }
        return null;
    }

    public function getCountryCode(): string
    {
        return $this->formatter->extractCountryIdFromVatId($this->getValue());
    }
}
