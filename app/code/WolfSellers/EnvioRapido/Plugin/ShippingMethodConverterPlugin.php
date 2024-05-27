<?php
namespace WolfSellers\EnvioRapido\Plugin;

use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;
use Magento\Quote\Model\Cart\ShippingMethodConverter;

/**
 *
 */
class ShippingMethodConverterPlugin
{

    /**
     * @var ShippingMethodExtensionFactory
     */
    protected $extensionFactory;

    /**
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(
        ShippingMethodExtensionFactory $extensionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param ShippingMethodConverter $subject
     * @param $result
     * @param $rateModel
     * @param $quoteCurrencyCode
     * @return mixed
     */
    public function afterModelToDataObject(ShippingMethodConverter $subject, $result, $rateModel, $quoteCurrencyCode){
        $extensionAttributes = $this->extensionFactory->create();
        $extensionAttributes->setDeliveryTime($rateModel->getData('delivery_time'));
        $result->setExtensionAttributes($extensionAttributes);
        return $result;
    }

}
