<?php
/**
 * Created by.
 *
 * User: Juan Carlos Hdez <juanhernandez@wolfsellers.com>
 * Date: 2022-03-30
 * Time: 21:28
 */

declare(strict_types=1);

namespace WolfSellers\Checkout\Block\Onepage;

use WolfSellers\Checkout\Block\Onepage\LayoutWalkerFactory;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;

/**
 * Onepage Layout Processor.
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /** @var LayoutWalkerFactory */
    private LayoutWalkerFactory $walkerFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;
    /**
     * @var Session
     */
    private Session $session;

    /**
     * @param \WolfSellers\Checkout\Block\Onepage\LayoutWalkerFactory $walkerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $session
     */
    public function __construct(LayoutWalkerFactory $walkerFactory, CustomerRepositoryInterface $customerRepository, Session $session)
    {
        $this->walkerFactory = $walkerFactory;
        $this->_customerRepository = $customerRepository;
        $this->session = $session;
    }

    /**
     * @param $jsLayout
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function process($jsLayout): array
    {
        $walker = $this->walkerFactory->create(['layoutArray' => $jsLayout]);
        $idCustomer =$this->session->getCustomerId();

        //CUSTOMER DATA AREA
        $customerDataComponent = [
            'component' => 'WolfSellers_Checkout/js/view/customer-data-step',
            'displayArea' => 'customer-data-step',
            'provider' => 'checkoutProvider',
            'sortOrder' => '0'
        ];
        $resumenCustomerData = [
            'component' => 'WolfSellers_Checkout/js/view/resumen-customer-data',
            'displayArea' => 'resumen-personal-information'
        ];
        $customerAddressArea = $customerDataComponent;
        $walker->setValue('{CHECKOUT_STEPS}.>>.customer-data-step', $customerAddressArea);
        //customer-fieldsets
        //Customer Data Nombre
        $customerDataNombreComponent = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'displayArea' => 'customer-data-firstname',
            'config' => [
                'customScope' => 'customerData.firstname',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'dataScope' => 'customerData.firstname',
            'label' => 'Nombre',
            'provider' => 'checkoutProvider',
            'sortOrder' => 1,
            'validation' => [
                'required-entry' => true
            ],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value'=> $this->getNameCustomer($idCustomer)
        ];
        $customerDataLastNameComponent = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'displayArea' => 'customer-data-lastname',
            'config' => [
                'customScope' => 'customerData.lastname',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'dataScope' => 'customerData.lastname',
            'label' => 'Apellido',
            'provider' => 'checkoutProvider',
            'sortOrder' => 2,
            'validation' => [
                'required-entry' => true
            ],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => $this->getLastNameCustomer($idCustomer)
        ];
        $customerDataIdentificacionComponent = [
            //'component' => 'Magento_Ui/js/form/element/abstract',
            'component' => 'WolfSellers_Checkout/js/view/form/element/select-custom-tipo_identificacion',
            'displayArea' => 'customer-data-identificacion',
            'config' => [
                'customScope' => 'customerData.identificacion',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'tooltip' => [
                    "description" => 'Selecciona tipo de identificacion.'
                ],

            ],
            'dataScope' => 'customerData.identificacion',
            'label' => 'Identificacion',
            'provider' => 'checkoutProvider',
            'validation' => [
                'required-entry' => true
            ],
            'sortOrder' => 3,
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'options' => [
                ["label"=>"Pasaporte","value"=>865],
                ["label"=>"DNI","value"=>868]
            ],
            'value' => $this->getIdentificacionCustomer($idCustomer)
        ];
        $customerDataNumeroIdentificacionComponent = [
            'component' => 'WolfSellers_Checkout/js/view/form/element/input-numero_identificacion',
            'displayArea' => 'customer-data-numero_de_identificacion',
            'config' => [
                'customScope' => 'customerData.numero_de_identificacion',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'dataScope' => 'customerData.numero_de_identificacion',
            'label' => 'Numero de Identificacion',
            'provider' => 'checkoutProvider',
            'sortOrder' => 4,
            'validation' => [
                'required-entry' => true
            ],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => $this->getNumIdentificacionCustomer($idCustomer)
        ];
        $customerDataTelefonoComponent = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'displayArea' => 'customer-data-telefono',
            'config' => [
                'customScope' => 'customerData.telefono',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'tooltip' => [
                    "description" => 'Ingresa Número de Telefono.'
                ],
            ],
            'dataScope' => 'customerData.telefono',
            'label' => 'Telefono',
            'provider' => 'checkoutProvider',
            'sortOrder' => 5,
            'validation' => [
                'required-entry' => true,
                'validate-number' => true,
                'min_text_length' => 7,
                'max_text_length' => 12
            ],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value'=>$this->getTelefonoCustomer($idCustomer)
        ];
        $customerDataAgreementComponent = [
            'component' => 'Magento_CheckoutAgreements/js/view/checkout-agreements'
        ];
        $customerDataTelefonoComponent = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'displayArea' => 'customer-data-telefono',
            'config' => [
                'customScope' => 'customerData.telefono',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'tooltip' => [
                    "description" => 'Ingresa Número de Telefono.'
                ],
            ],
            'dataScope' => 'customerData.telefono',
            'label' => 'Telefono',
            'provider' => 'checkoutProvider',
            'sortOrder' => 5,
            'validation' => [
                'required-entry' => true,
                'validate-number' => true,
                'min_text_length' => 7,
                'max_text_length' => 12
            ],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value'=>$this->getTelefonoCustomer($idCustomer)
        ];
        $customerFieldsets = [
          'component' => 'uiComponent',
          'displayArea' => 'customer-fieldsets'
        ];
        $customerDataFieldSets = $walker->getValue('{CUSTOMER-DATA}.>>');
        $customerDataFieldSets['customer-fieldsets'] = $customerFieldsets;
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-firstname'] = $customerDataNombreComponent;
        //APELLIDO
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-lastname'] = $customerDataLastNameComponent;
        //TIPODEIDENTIFICACION
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-identificacion'] = $customerDataIdentificacionComponent;
        //IDENTIFICACION
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-numero_de_identificacion'] = $customerDataNumeroIdentificacionComponent;
        //NUMEROCELULAR
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-telefono'] = $customerDataTelefonoComponent;
        //TERMINOSYCONDICIONES
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-agreement'] = $customerDataAgreementComponent;
        $customerDataFieldSets['customer-email'] = $walker->getValue('{SHIPPING_ADDRESS}.>>.customer-email');
        $customerDataFieldSets['customer-data-resumen'] = $resumenCustomerData;
        $walker->setValue('{CUSTOMER-DATA}.>>', $customerDataFieldSets);
        $walker->setValue('{SHIPPING_ADDRESS}.>>.customer-email', []);
        $walker->setValue('{PAYMENT}.>>.customer-email', []);

      //  $email= $walker->getValue('{SHIPPING_ADDRESS}.>>.customer-email');
        //******SHIPPING ADDRESS******
        //COMPANY
        $company = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.company');
        $company['visible'] = false;
        $company['sortOrder'] = 200;
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.company', $company);
        //DNI
        $dni = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.dni');
        $dni['visible'] = false;
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.dni', $dni);
        //VAT ID
        $vat = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.vat_id');
        $vat['visible'] = false;
        $vat['imports'] = [
            'visible' => '${ $.parentName }.invoice_required:value',
        ];
        $vat['validation'] = array_merge($vat['validation'], [
            'required-entry' => true,
        ]);
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.vat_id', $vat);
        //CITY
        $city = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.city');
        $city['component'] = 'WolfSellers_Checkout/js/view/form/element/city';
        $city['validation'] = ['required-entry' => true];
        $city['config']['elementTmpl'] = 'ui/form/element/select';
        $city['filterBy'] = [
            'field' => 'region_id',
            'target' => '${ $.provider }:${ $.parentScope }.region_id',
        ];
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.city', $city);
        //COLONY
        $colony = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.colony');
        $colony['component'] = 'WolfSellers_Checkout/js/view/form/element/colony';
        $colony['validation'] = ['required-entry' => true];
        $colony['config']['elementTmpl'] = 'ui/form/element/select';
        $colony['config']['options'] = [[
            'label' => '',
            'value' => '',
        ]];
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.colony', $colony);
        //DOB
        $fechaNacimiento = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.fecha_de_nacimiento');
        $fechaNacimiento['component'] = 'WolfSellers_Checkout/js/view/form/element/birthdate';
        $fechaNacimiento['config']['elementTmpl'] = 'WolfSellers_Checkout/form/element/birthdate';
        $fechaNacimiento['validation'] = [];
        $fechaNacimiento['options'] = [
            'yearRange' => '-60:-10',
            'dateFormat' => 'dd/mm/yy',
            'altField' => '#custom_birthdate',
            'altFormat' => 'mm/dd/yy',
        ];
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.fecha_de_nacimiento', $fechaNacimiento);
        //PAYMENTS AREA
        $payments = $walker->getValue('{PAYMENT}.>>.payments-list');
        foreach ($payments['children'] as &$payment) {
            if (!empty($payment['children'])
                && !empty($payment['children']['form-fields'])
                && !empty($payment['children']['form-fields']['children'])
                && !empty($payment['children']['form-fields']['children']['fecha_de_nacimiento'])
            ) {
                $payment['children']['form-fields']['children']['fecha_de_nacimiento']['validation'] = [];
            }
        }
        $walker->setValue('{PAYMENT}.>>.payments-list', $payments);

        return $walker->getResult();
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
