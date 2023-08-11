<?php

namespace WolfSellers\Bopis\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Psr\Log\LoggerInterface;

class ShippingOptions implements ArrayInterface
{

    /** @var \Magento\Shipping\Model\Carrier\AbstractCarrierInterface[]  */
    private $allDeliveryMethods;

    /**
     * @param Config $_deliveryModelConfig
     * @param ScopeConfigInterface $_scopeConfig
     * @param Collection $_ordersCollection
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected Config $_deliveryModelConfig,
        protected ScopeConfigInterface $_scopeConfig,
        protected Collection $_ordersCollection,
        protected LoggerInterface $logger
    )
    {
        $this->allDeliveryMethods = $this->_deliveryModelConfig->getAllCarriers();
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $mainOrders = $this->_ordersCollection
            ->distinct(true)
            ->addAttributeToSelect('shipping_method')
            ->addAttributeToSelect('shipping_description')
            ->load();

        $options = [];

        try {
            foreach ($mainOrders as $data){
                $methodCode = trim($data->getShippingDescription());
                $options[$methodCode] = $methodCode . ' (' .$data->getShippingMethod(). ')';
            }
        }catch (\Throwable $e){
            $this->logger->error($e->getMessage());
        }

        $options[''] = 'Todo';

        return $options;
    }

    /**
     * @param $methodCode
     * @return mixed
     */
    private function getAlias($methodCode)
    {
        foreach ($this->allDeliveryMethods as $deliveryCode => $deliveryModel){
            if (str_contains($methodCode, $deliveryCode)){
                $deliveryTitle = $this->_scopeConfig->getValue('carriers/'.$deliveryCode.'/title');
                return $deliveryTitle;
            }
        }

        return $methodCode;
    }
}
