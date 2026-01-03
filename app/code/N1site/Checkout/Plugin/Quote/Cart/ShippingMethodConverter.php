<?php

namespace N1site\Checkout\Plugin\Quote\Cart;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use N1site\Checkout\Helper\Data;

class ShippingMethodConverter
{
    /**
     * @var ExtensionAttributesFactory
     */
    protected $extensionAttributesFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param ExtensionAttributesFactory $extensionAttributesFactory
     * @param Data $helper
     */
    public function __construct(
        ExtensionAttributesFactory $extensionAttributesFactory,
        Data $helper
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
        $this->helper = $helper;
    }

    /**
     * Add extension atttributes to shipping method
     *
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $subject
     * @param $result
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel The rate model.
     * @param string $quoteCurrencyCode The quote currency code.
     */
    public function afterModelToDataObject(
        \Magento\Quote\Model\Cart\ShippingMethodConverter $subject,
        $result,
        $rateModel,
        $quoteCurrencyCode
    ) {
        $extensionAttributes = $result->getExtensionAttributes();
        $carrier = $rateModel->getCarrier();
        /** @var \Magento\Quote\Api\Data\ShippingMethodExtensionInterface $shippingExtension */
        $shippingExtension = $extensionAttributes ?
            $extensionAttributes : $this->extensionAttributesFactory->create(ShippingMethodInterface::class);
        $logoUrl = $this->helper->getCarrierLogoUrl($carrier);
        $description = $this->helper->getCarrierDescription($carrier);
        $shippingExtension->setLogoUrl($logoUrl);
        $shippingExtension->setDescription($description);
        $result->setExtensionAttributes($shippingExtension);

        return $result;
    }
}