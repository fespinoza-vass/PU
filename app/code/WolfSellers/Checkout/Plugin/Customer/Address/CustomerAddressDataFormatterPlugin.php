<?php

namespace WolfSellers\Checkout\Plugin\Customer\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteRepository;

class CustomerAddressDataFormatterPlugin
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
     * @param \Magento\Customer\Model\Address\CustomerAddressDataFormatter $subject
     * @param AddressInterface $customerAddress
     * @return array|AddressInterface[]|null[]
     */
    public function beforePrepareAddress(
        \Magento\Customer\Model\Address\CustomerAddressDataFormatter $subject,
        AddressInterface $customerAddress
    ) {
        $updateAddress = $this->updateCustomAddressAttributes($customerAddress);
        $customerAddress = $updateAddress;

        return [$customerAddress];
    }

    /**
     * @param $address
     * @return AddressInterface|mixed|void
     */
    public function updateCustomAddressAttributes($address){

        try{
            if($address){
                $update = false;
                $resultAddress = $this->addressRepository->getById($address->getId());
                $address->getCustomAttribute('ruc') ?? $resultAddress->setCustomAttribute('ruc','') && $update = true;
                $address->getCustomAttribute('razon_social') ?? $resultAddress->setCustomAttribute('razon_social','') && $update = true;
                $address->getCustomAttribute('direccion_fiscal') ?? $resultAddress->setCustomAttribute('direccion_fiscal','') && $update = true;
                if($update){
                    $this->addressRepository->save($resultAddress);
                    return $resultAddress;
                }else{
                    return $address;
                }
            }
        }catch (\Exception $exception) {
        }
    }
}
