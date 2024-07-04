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
        $quote = $this->quoteRepository->getActive($cartId);

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

            $extension = $this->_addressExtensionFactory->create();

            $extension->setData('referencia_envio',$extensionAttributes->getEnvioRapido()->getReferencia());
            $extension->setData('distrito_envio_rapido',$extensionAttributes->getEnvioRapido()->getDistrito());

            $quote->getBillingAddress()->setExtensionAttributes($extension);
            $quote->getShippingAddress()->setExtensionAttributes($extension);


            $horarioValue = $this->getIdOptionByValue(
                                'horarios_disponibles',
                                 $extensionAttributes->getEnvioRapido()->getHorarioSeleccionado()
                            );

            $quote->getShippingAddress()->setData('horarios_disponibles',$horarioValue);
            $quote->getShippingAddress()->setCustomAttribute('horarios_disponibles',$horarioValue);

            $quote->getShippingAddress()->setData('colony',$extensionAttributes->getEnvioRapido()->getDistrito());
            $quote->getShippingAddress()->setCustomAttribute('colony',$extensionAttributes->getEnvioRapido()->getDistrito());


            $quote->save();
        }

        if($extensionAttributes->getEnvioUrbano()->getDistrito()){

            $extension = $this->_addressExtensionFactory->create();

            $extension->setData('referencia_envio',$extensionAttributes->getEnvioUrbano()->getReferencia());

            $quote->getBillingAddress()->setExtensionAttributes($extension);
            $quote->getShippingAddress()->setExtensionAttributes($extension);

            $quote->getShippingAddress()->setData('colony',$extensionAttributes->getEnvioUrbano()->getDistrito());
            $quote->getShippingAddress()->setCustomAttribute('colony',$extensionAttributes->getEnvioUrbano()->getDistrito());

            $quote->save();
        }

        if($extensionAttributes->getEnvioRegular()->getDistrito()){

            $extension = $this->_addressExtensionFactory->create();

            $extension->setData('referencia_envio',$extensionAttributes->getEnvioRegular()->getReferencia());

            $quote->getBillingAddress()->setExtensionAttributes($extension);
            $quote->getShippingAddress()->setExtensionAttributes($extension);

            $quote->getShippingAddress()->setData('colony',$extensionAttributes->getEnvioRegular()->getDistrito());
            $quote->getShippingAddress()->setCustomAttribute('colony',$extensionAttributes->getEnvioRegular()->getDistrito());

            $quote->save();
        }

        if ($extensionAttributes->getRetiroTienda()->getPicker()) {

            $picker = $this->getIdOptionByValue('picker', $extensionAttributes->getRetiroTienda()->getPicker());
            $quote->getShippingAddress()->setData('picker', $picker);
            $quote->getShippingAddress()->setCustomAttribute('picker', $picker);

            $quote->getShippingAddress()->setData('identificacion_picker', $extensionAttributes->getRetiroTienda()->getIdentificacion());
            $quote->getShippingAddress()->setCustomAttribute('identificacion_picker', $extensionAttributes->getRetiroTienda()->getIdentificacion());

            $quote->getShippingAddress()->setData('numero_identificacion_picker',
                $extensionAttributes->getRetiroTienda()->getNumeroIdentificacion()
            );
            $quote->getShippingAddress()->setCustomAttribute('numero_identificacion_picker',
                $extensionAttributes->getRetiroTienda()->getNumeroIdentificacion()
            );

            $quote->getShippingAddress()->setData('nombre_completo_picker',
                $extensionAttributes->getRetiroTienda()->getNombreApellido()
            );
            $quote->getShippingAddress()->setCustomAttribute('nombre_completo_picker',
                $extensionAttributes->getRetiroTienda()->getNombreApellido()
            );

            $quote->getShippingAddress()->setData('email_picker',
                $extensionAttributes->getRetiroTienda()->getCorreoOpcional()
            );
            $quote->getShippingAddress()->setCustomAttribute('email_picker',
                $extensionAttributes->getRetiroTienda()->getCorreoOpcional()
            );
            /********* Currently this information is not used ***********
             ************************************************************
            $quote->getShippingAddress()->setData('distrito_pickup',
                $extensionAttributes->getRetiroTienda()->getDistritoComprobante()
            );
            $quote->getShippingAddress()->setCustomAttribute('distrito_pickup',
                $extensionAttributes->getRetiroTienda()->getDistritoComprobante()
            );
            $quote->getShippingAddress()->setData('direccion_comprobante_picker',
                $extensionAttributes->getRetiroTienda()->getDireccionComprobante()
            );
            $quote->getShippingAddress()->setCustomAttribute('direccion_comprobante_picker',
                $extensionAttributes->getRetiroTienda()->getDireccionComprobante()
            );
            **************************************************************
            **/

            $quote->save();
        }
    }

    public function getIdOptionByValue($attributeCode,$value){
        $optionId = null;
        $attribute = $this->_eavConfig->getAttribute('customer_address', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        foreach($options as $option) {
            if ($option['label'] == $value) {
                $optionId = $option['value'];
            }
        }
        return $optionId;
    }
}
