<?php

namespace WolfSellers\EnvioRapido\Plugin;

use Magento\Eav\Model\Config;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;


/**
 *
 */
class PlaceOrderPlugin
{
    /** @var OrderRepositoryInterface */
    protected $_orderRepository;
    /** @var Config */
    protected $_eavConfig;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param Config $eavConfig
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Config $eavConfig
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_eavConfig = $eavConfig;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $result
    ) {
        try{
            $order = $result;
            $shippingAddress =$order->getShippingAddress();
            if($shippingAddress->getData('horarios_disponibles')){
                $horario = $this->getValueByOptionId(
                    'horarios_disponibles',
                    $shippingAddress->getData('horarios_disponibles')
                );
                $order->setSavarHorario($horario);
                $this->_orderRepository->save($order);
            }
        }catch (\Throwable $error){

        }

        return $result;
    }

    /**
     * @param $attributeCode
     * @param $optionId
     * @return mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValueByOptionId($attributeCode, $optionId)
    {
        $label = null;
        $attribute = $this->_eavConfig->getAttribute('customer_address', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $optionId) {
                $label = $option['label'];
            }
        }
        return $label;
    }
}
