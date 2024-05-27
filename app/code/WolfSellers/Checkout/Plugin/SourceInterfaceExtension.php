<?php
namespace WolfSellers\Checkout\Plugin;

use Magento\InventoryApi\Api\Data\SourceInterface;

class SourceInterfaceExtension
{
    public function afterGetExtensionAttributes(SourceInterface $source, $extensionAttributes)
    {
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }
        $extensionAttributes->setDistrict($source->getData('district'));
        return $extensionAttributes;
    }
}
