<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-07-19
 * Time: 17:59
 */

declare(strict_types=1);

namespace WolfSellers\SalesOrder\Plugin\Sales\Api;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Order Repository Api Plugin.
 */
class OrderRepositoryPlugin
{
    /**
     * Item get data.
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        try{
            $billingAddress = $order->getBillingAddress();
            $billingAddressExtensionAttributes = $billingAddress->getExtensionAttributes();
            $billingAddressExtensionAttributes->setColony($billingAddress->getColony()??"");
            $billingAddressExtensionAttributes->setReferenciaEnvio($billingAddress->getReferenciaEnvio()??"");
            $billingAddressExtensionAttributes->setRuc($billingAddress->getRuc()??"");
            $billingAddressExtensionAttributes->setRazonSocial($billingAddress->getRazonSocial()??"");
            $billingAddressExtensionAttributes->setIdentificacion($order->getCustomerIdenficacion()=="868" ? "DNI" : "Pasaporte");


            $shippingAddress = $order->getShippingAddress();

            $orderExtensionAttributes = $order->getExtensionAttributes();
            if ($shipmentAssigments = $orderExtensionAttributes->getShippingAssignments()) {
                /** @var ShippingAssignmentInterface $shippingAssignment */
                $shipmentAssigment = $shipmentAssigments[0];
                $shipping = $shipmentAssigment->getShipping();
                $shippingExtensionAttributes = $shipping->getAddress()->getExtensionAttributes();
                $shippingExtensionAttributes->setColony($shippingAddress->getColony());
                $shippingExtensionAttributes->setCompany($shippingAddress->getCompany());
                $shippingExtensionAttributes->setRuc($shippingAddress->getDni());
                $shippingExtensionAttributes->setReferenciaEnvio($shippingAddress->getReferenciaEnvio());
            }
        } catch (\Throwable $error){

        }
        return $order;
    }

    /**
     * Add custom attributes.
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     *
     * @see OrderRepositoryInterface::getList()
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        foreach ($searchResult->getItems() as $entity) {
            $this->afterGet($subject, $entity);
        }

        return $searchResult;
    }
}
