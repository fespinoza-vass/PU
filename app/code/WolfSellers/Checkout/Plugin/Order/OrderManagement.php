<?php

namespace WolfSellers\Checkout\Plugin\Order;

use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;

class OrderManagement
{

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
        QuoteRepository $quoteRepository,
        AddressRepositoryInterface $addressRepository
    ) {
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
