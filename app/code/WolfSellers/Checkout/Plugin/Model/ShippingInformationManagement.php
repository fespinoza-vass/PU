<?php

namespace WolfSellers\Checkout\Plugin\Model;

use Magento\Quote\Model\QuoteRepository;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Quote\Api\Data\AddressExtensionFactory;

class ShippingInformationManagement
{

    /** @var AddressExtensionFactory */
    protected $_addressExtensionFactory;
    /** @var Config  */
    protected $_eavConfig;
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
        CustomerRepositoryInterface $customerRepository,
        Config $eavConfig,
        AddressExtensionFactory $addressExtensionFactory
    ) {
        $this->_addressExtensionFactory = $addressExtensionFactory;
        $this->_eavConfig = $eavConfig;
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

        // atributos de envio rapido
        if($extensionAttributes->getEnvioRapido()->getDistrito()){

            $addressInformation->getShippingAddress()->setCustomAttribute('referencia_envio',$extensionAttributes->getEnvioRapido()->getReferencia());
            $addressInformation->getBillingAddress()->setCustomAttribute('referencia_envio',$extensionAttributes->getEnvioRapido()->getReferencia());

            $addressExtension = $this->_addressExtensionFactory->create();

            $addressExtension->setData('distrito_envio_rapido',$extensionAttributes->getEnvioRapido()->getDistrito());
            $addressExtension->setData('horarioSeleccionado',$extensionAttributes->getEnvioRapido()->getHorarioSeleccionado());

            $addressInformation->getShippingAddress()->setExtensionAttributes($addressExtension);
            $addressInformation->getBillingAddress()->setExtensionAttributes($addressExtension);
        }

    }

    public function getIdOptionByValue($attributeCode,$value){
        $optionId = null;
        $attribute = $this->_eavConfig->getAttribute('customer_address', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        foreach($options as $option) {
            var_dump($option);
            if ($option['label'] == $value) {
                $optionId = $option['id'];
            }
        }
        return $optionId;
    }
}
