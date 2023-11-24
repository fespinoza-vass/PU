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
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $result
    ) {
        $orderId = $result->getIncrementId();
        if ($orderId) {
            try {
                $billingAddress =$result->getBillingAddress();
                $address = $result->getAddresses();

                $quote = $this->quoteRepository->get($result->getQuoteId());

                if($quote->getCustomerName()){
                    $result->setCustomerNombre($quote->getCustomerName());
                    $result->setCustomerApellido($quote->getCustomerApellido());
                    $result->setCustomerTelefono($quote->getCustomerTelefono());
                    $result->setCustomerIdentificacion($quote->getCustomerIdentificacion());
                    $result->setCustomerNumeroDeIdentificacion($quote->getCustomerNumeroDeIdentificacion());

                    $result->getBillingAddress()->setFirstname($quote->getCustomerName());
                    $result->getBillingAddress()->setLastname($quote->getCustomerApellido());

                    $this->_orderRepositoryInterface->save($result);
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
        return $result;
    }
}
