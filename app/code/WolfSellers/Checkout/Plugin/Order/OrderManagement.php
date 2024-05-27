<?php

namespace WolfSellers\Checkout\Plugin\Order;

use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderManagement
{

    /** @var OrderRepositoryInterface */
    protected $_orderRepositoryInterface;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @param QuoteRepository $quoteRepository
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        QuoteRepository $quoteRepository,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->_orderRepositoryInterface = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface $result
     * @return OrderInterface
     */
    public function beforePlace(
        OrderManagementInterface $subject,
        OrderInterface $order
    ) {
        $orderId = $order->getIncrementId();
        if ($orderId) {
            try {
                $billingAddress =$order->getBillingAddress();
                $address = $order->getAddresses();

                $quote = $this->quoteRepository->get($order->getQuoteId());

                if($order->getShippingMethod() == "instore_pickup"){
                    if($order->getCustomerIsGuest()){
                        $order->setCustomerNombre($quote->getCustomerName());
                        $order->setCustomerApellido($quote->getCustomerApellido());
                        $order->setCustomerTelefono($quote->getCustomerTelefono());
                        $order->setCustomerIdentificacion($quote->getCustomerIdentificacion());
                        $order->setCustomerNumeroDeIdentificacion($quote->getCustomerNumeroDeIdentificacion());

                        $order->getBillingAddress()->setFirstname($quote->getCustomerName());
                        $order->getBillingAddress()->setLastname($quote->getCustomerApellido());

                        $order->setCustomerFirstname($quote->getCustomerName()." ".$quote->getCustomerApellido());
                        $order->setCustomerLastname('');
                        $this->_orderRepositoryInterface->save($order);
                    }else{

                        $order->getBillingAddress()->setFirstname($order->getCustomerFirstname());
                        $order->getBillingAddress()->setLastname($order->getCustomerLastname());

                        $this->_orderRepositoryInterface->save($order);
                    }
                }

                foreach ($address as $item){
                    $item->getQuoteAddressId();
                    $addressId = $item->getCustomerAddressId();
                    $address = $this->addressRepository->getById($addressId);
                    $address->setCustomAttribute('ruc',$billingAddress->getRuc());
                    $address->setCustomAttribute('razon_social',$billingAddress->getRazonSocial());
                    $address->setCustomAttribute('direccion_fiscal',$billingAddress->getDireccionFiscal());

                    $this->addressRepository->save($address);
                }
            } catch (\Exception $exception) {
            }
        }
        return [$order];
    }
}
