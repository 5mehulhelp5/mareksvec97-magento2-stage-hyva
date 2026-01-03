<?php

declare(strict_types=1);

namespace Hyva\GeisswebEuvatCheckout\ViewModel\Validation;

use Geissweb\Euvat\Model\ValidationRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Data implements ArgumentInterface
{
    private ValidationRepository $validationRepository;
    private Json $jsonSerializer;

    public function __construct(
        ValidationRepository $validationRepository,
        Json $jsonSerializer
    ) {
        $this->validationRepository = $validationRepository;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function getByVatId(string $vatId): bool|string {
        if (empty($vatId)) {
            return false;
        }
        $result = $this->validationRepository->getByVatId($vatId);
        if($result === false) {
            return false;
        }
        return $this->jsonSerializer->serialize([
            'vat_is_valid' => $result->getVatIsValid(),
            'vat_request_success' => $result->getVatRequestSuccess(),
            'request_message' => $result->getRequestMessage()
        ]);
    }
}
