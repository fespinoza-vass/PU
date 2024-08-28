<?php

declare(strict_types=1);

namespace Vass\Checkout\Plugin\Block\Checkout;

use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;

class LayoutProcessor
{
    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;
    
    /**
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Session $session,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->session = $session;
        $this->_customerRepository = $customerRepository;

    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
        
    ) {
        $idCustomer =$this->session->getCustomerId();


            // var_dump($this->getIdentificacionCustomer($idCustomer));
            // var_dump($this->getNumIdentificacionCustomer($idCustomer));
        
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['ruc']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['razon_social']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['direccion_fiscal']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['referencia_envio'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',  // Ajusta este componente si es necesario
            'config' => [
                'sortOrder' => 150,
            ],
            'dataScope' => 'shippingAddress.custom_attributes.referencia_envio',
            'label' => __('Referencia de Envío'),
            'provider' => 'checkoutProvider',
            'visible' => true,
        ];
        
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['dni']['visible']=false;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['numero_identificacion_picker'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'sortOrder' => 35,
            ],
            'dataScope' => 'shippingAddress.custom_attributes.numero_identificacion_picker',
            'label' => __('Número de Identificación'),
            'provider' => 'checkoutProvider',
            'visible' => true,
        ];
        
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['nombre_completo_picker']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['email_picker']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['direccion_comprobante_picker']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['invoice_required']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['distrito_envio_rapido']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['horarios_disponibles']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['comprobante_pickup']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['distrito_pickup']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['vat_id']['visible']=false; 
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['picker']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['visible']=true; 
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['observable']=false; 
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['visible'] =false; 

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['visible']=false;
            
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'sortOrder' => 40,
                'template' => 'ui/form/field',
            ],
            'dataScope' => 'shippingAddress.telephone',
            'label' => __('Número de teléfono'),
            'placeholder' => __('Ingrese número de teléfono'),
            'provider' => 'checkoutProvider',
            'visible' => true,
        ];    

            //validacion select
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children']['identificacion_picker'] = [
                'component' => 'Magento_Ui/js/form/element/select',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'dataScope' => 'shippingAddress.custom_attributes.identificacion_picker',
                    'label' => __('Tipo de Documento'),
                    'provider' => 'checkoutProvider',
                    'visible' => true,
                    'options' => [
                        ['value' => '', 'label' => __('Seleccione un tipo de documento')],
                        ["label"=>"DNI","value"=>868],
                        ["label"=>"Pasaporte","value"=>865]
                    ],
                    'value' => $this->getIdentificacionCustomer($idCustomer),
                    'validation' => [
                        'required-entry' => true
                    ],
                ],
                'sortOrder' => 30,
                'dataType' => 'text',
                'label' => __('Tipo de Documento'),
                'provider' => 'checkoutProvider',
                'visible' => true,
            ];

             //telephone
      
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone'])
            ) {
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['config']['validation'] = [
                    'required-entry' => true, 
                    'max_text_length' => 9
                ];
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['config']['errorMessage'] = __('El número de DNI debe tener 10 dígitos.');
            }

            $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['comment']['visible']= false;
            if (isset($jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['checkboxes']['children']['subscribe'])) {
                // Oculta el checkbox 'subscribe'
                $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['checkboxes']['children']['subscribe']['displayArea'] = 'hidden';
            }

      

            $check_terminos = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'checkout.sidebar.additional.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/checkbox',
                    'id' => 'checkbox_terminos',
                ],
                'dataScope' => 'checkout.sidebar.additional.checkbox_terminos',
                'description' => '', // Cambia esto a la etiqueta deseada
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                    'required-entry' => false, // Cambia a true si deseas que sea obligatorio
                ],
                'sortOrder' => 100, // Cambia según donde quieras que aparezca
                'id' => 'custom-checkbox-terminos',
                
            ];

            $checkbox_privacidad = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'checkout.sidebar.additional.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/checkbox',
                    'id' => 'checkbox_privacidad',
                ],
                'dataScope' => 'checkout.sidebar.additional.checkbox_privacidad',
                'description' => '', // Cambia esto a la etiqueta deseada
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                    'required-entry' => false, // Cambia a true si deseas que sea obligatorio
                ],
                'sortOrder' => 200, // Cambia según donde quieras que aparezca
                'id' => 'custom-checkbox-privacidad',
                
            ];

            $checkbox_newsletter = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'checkout.sidebar.additional.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/checkbox',
                    'id' => 'checkbox_newsletter',
                ],
                'dataScope' => 'checkout.sidebar.additional.checkbox_newsletter',
                'description' => '', // Cambia esto a la etiqueta deseada
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                    'required-entry' => false, // Cambia a true si deseas que sea obligatorio
                ],
                'sortOrder' => 300, // Cambia según donde quieras que aparezca
                'id' => 'custom-checkbox-newsletter',
                
            ];


            

            // Añadiendo el checkbox a la ruta especificada
            $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['custom_checkbox'] = $check_terminos;
            $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['custom_checkbox2'] = $checkbox_privacidad;
            $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['custom_checkbox3'] = $checkbox_newsletter;

            // Campo City
            $cityField = [
                'component' => 'Vass_Checkout/js/form/element/custom-select-address-city',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/select',
                    'id' => 'city',
                ],
                'dataScope' => 'shippingAddress.custom_attributes.city',
                'label' => 'Provincia',
                'provider' => 'checkoutProvider',
                'sortOrder' => 210,
                'validation' => [
                    'required-entry' => true
                ],
                'options' => [[
                    'label' => '',
                    'value' => '',
                ]], 
                'filterBy' => [
                    'target' => 'shippingAddress.region_id',
                    'field' => 'region_id',
                ],
                'visible' => true,
                'imports' => [
                    'initialOptions' => 'index = checkoutProvider:dictionaries.city',
                    'setOptions' => 'index = checkoutProvider:dictionaries.city',
                ],
                'observable' => true
            ];

            // Campo Colony
            $colonyField = [
                'component' => 'Vass_Checkout/js/form/element/custom-select-address-colony',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/select',
                    'id' => 'colony',
                ],
                'dataScope' => 'shippingAddress.custom_attributes.colony',
                'label' => 'Distrito',
                'provider' => 'checkoutProvider',
                'sortOrder' => 220,
                'validation' => [
                    'required-entry' => true
                ],
                'options' =>[[
                    'label' => '',
                    'value' => '',
                ]],
                'filterBy' => [
                    'target' => 'shippingAddress.custom_attributes.city',
                    'field' => 'city',
                ],
                'visible' => true,
                'imports' => [
                    'initialOptions' => 'index = checkoutProvider:dictionaries.colony',
                    'setOptions' => 'index = checkoutProvider:dictionaries.colony',
                ],
                'observable' => true
            ];

            // Añadir los campos al layout
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = $cityField;
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['colony'] = $colonyField;



            //precargargamos inputs si existen cuenta registrada
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['value'] = $this->getNameCustomer($idCustomer);

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']['value'] = $this->getLastNameCustomer($idCustomer);

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['value'] = $this->getTelefonoCustomer($idCustomer);

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['numero_identificacion_picker']['value'] = $this->getNumIdentificacionCustomer($idCustomer);
            
        return $jsLayout;

        
    }

        /**
     * @param $idCustomer
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public  function  getNameCustomer($idCustomer){
        $nameCustomer = "";
        if(isset($idCustomer)){
            $customer = $this->_customerRepository->getById($idCustomer);
            $nameCustomer = $customer->getFirstname();
        }
        return $nameCustomer;
    }

    /**
     * @param $idCustomer
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public  function  getLastNameCustomer($idCustomer){
        $lastnameCustomer = "";
        if(isset($idCustomer)){
            $customer = $this->_customerRepository->getById($idCustomer);
            $lastnameCustomer = $customer->getLastname();
        }
        return $lastnameCustomer;
    }

    /**
     * @param $idCustomer
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public  function  getIdentificacionCustomer($idCustomer){
        $typeCustomer = 'default';
        if(isset($idCustomer)){
            $customer = $this->_customerRepository->getById($idCustomer);
            $typeCustomer = $customer->getCustomAttribute('identificacion')->getValue();
        }
        return $typeCustomer;
    }

    /**
     * @param $idCustomer
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public  function  getNumIdentificacionCustomer($idCustomer){
        $numIdCustomer = "";
        if(isset($idCustomer)){
            $customer = $this->_customerRepository->getById($idCustomer);
            $numIdCustomer = $customer->getCustomAttribute('numero_de_identificacion')->getValue();
        }
        return $numIdCustomer;
    }

    /**
     * @param $idCustomer
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public  function getTelefonoCustomer($idCustomer){
        $telCustomer = "";
        if(isset($idCustomer)){
            $customer = $this->_customerRepository->getById($idCustomer);
            $telCustomer = $customer->getCustomAttribute('telefono')->getValue();
        }
        return $telCustomer;
    }

}
