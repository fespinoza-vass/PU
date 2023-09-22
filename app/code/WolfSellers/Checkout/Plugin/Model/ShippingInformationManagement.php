<?php

namespace WolfSellers\Checkout\Plugin\Model;

use Magento\Quote\Model\QuoteRepository;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ShippingInformationManagement
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Session $sessionRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->sessionRepository = $sessionRepository;
        $this->customerRepository = $customerRepository;
    }


    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
                                                              $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extensionAttributes = $addressInformation->getExtensionAttributes();
        if(!$extAttributes = $addressInformation->getExtensionAttributes())
        {
            return;
        }

        if ($this->sessionRepository->isLoggedIn()){
            $idCustomer = $this->sessionRepository->getCustomerId();
            $customer = $this->customerRepository->getById($idCustomer);
            $customer->setFirstname($extAttributes->getCustomerName());
            $customer->setLastname($extAttributes->getCustomerApellido());
            $customer->setCustomAttribute('telefono',$extensionAttributes->getCustomerTelefono());
            $customer->setCustomAttribute('identificacion', $extensionAttributes->getCustomerIdentificacion());
            $customer->setCustomAttribute('numero_de_identificacion',$extensionAttributes->getCustomerNumeroDeIdentificacion());
            $this->customerRepository->save($customer);
        }else{
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setCustomerName($extAttributes->getCustomerName());
            $quote->setCustomerApellido($extAttributes->getCustomerApellido());
            $quote->setCustomerTelefono($extensionAttributes->getCustomerTelefono());
            $quote->setCustomerIdentificacion($extensionAttributes->getCustomerIdentificacion());
            $quote->setCustomerNumeroDeIdentificacion($extensionAttributes->getCustomerNumeroDeIdentificacion());
            $quote->setCustomerPassword($extensionAttributes->getCustomerPassword());
            $quote->save();
        }
    }
}
