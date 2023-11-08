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
use WolfSellers\Checkout\Helper\Source as SourceHelper;

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

    /** @var SourceHelper */
    protected $_sourceHelper;

    /**
     * @param \WolfSellers\Checkout\Block\Onepage\LayoutWalkerFactory $walkerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session $session
     */
    public function __construct(
        LayoutWalkerFactory $walkerFactory,
        CustomerRepositoryInterface $customerRepository,
        Session $session,
        SourceHelper $sourceHelper
    ) {
        $this->walkerFactory = $walkerFactory;
        $this->_customerRepository = $customerRepository;
        $this->session = $session;
        $this->_sourceHelper = $sourceHelper;
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
        //CHECKOUT default template && default component
        $checkout = $walker->getValue('{CHECKOUT}');
        $checkout['component'] = "WolfSellers_Checkout/js/view/onepage";
        $checkout['config']['template'] = "WolfSellers_Checkout/onepage";
        $walker->setValue('{CHECKOUT}',$checkout);
        //CUSTOMER DATA STEP
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
        //CUSTOMER DATA FIELDS
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
            'label' => 'Tipo de documento',
            'provider' => 'checkoutProvider',
            'validation' => [
                'required-entry' => true
            ],
            'sortOrder' => 3,
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'options' => [
                ["label"=>"DNI","value"=>868],
                ["label"=>"Pasaporte","value"=>865]
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
            'label' => 'Número de Identificación',
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
        $customerDataAgreementComponent = [
            'component' => 'WolfSellers_Checkout/js/view/customer-agrements'
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
            'label' => 'Número de celular',
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
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-lastname'] = $customerDataLastNameComponent;
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-identificacion'] = $customerDataIdentificacionComponent;
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-numero_de_identificacion'] = $customerDataNumeroIdentificacionComponent;
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-telefono'] = $customerDataTelefonoComponent;
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-agreement'] = $customerDataAgreementComponent;
        $customerDataFieldSets['customer-email'] = $walker->getValue('{SHIPPING_ADDRESS}.>>.customer-email');
        $customerDataFieldSets['customer-data-resumen'] = $resumenCustomerData;
        $walker->setValue('{CUSTOMER-DATA}.>>', $customerDataFieldSets);
        $walker->setValue('{SHIPPING_ADDRESS}.>>.customer-email', []);
        $walker->setValue('{PAYMENT}.>>.customer-email', []);
        $walker->setValue('{STORE-PICKUP}.>>.customer-email', []);
        $company = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.company');
        $company['visible'] = false;
        $company['sortOrder'] = 200;
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.company', $company);
        $dni = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.dni');
        $dni['visible'] = false;
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.dni', $dni);
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
        //Step Two configuration
        $shippingRegular = [
            'component' => 'uiComponent',
            'displayArea' => 'regular',
            'provider' => 'checkoutProvider'
        ];
        $shippingFast= [
            'component' => 'uiComponent',
            'displayArea' => 'fast',
            'provider' => 'checkoutProvider'
        ];
        $shippingRegularArea = $walker->getValue('{SHIPPING_ADDRESS}.>>');
        $shippingRegularArea['regular'] = $shippingRegular;
        $shippingRegularArea['regular']['children']['departamento'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.region_id');
        $shippingRegularArea['regular']['children']['hidden-region'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.region');
        $shippingRegularArea['regular']['children']['provincia'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.city');
        $shippingRegularArea['regular']['children']['distrito'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.colony');
        $shippingRegularArea['regular']['children']['direccion'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.street');
        $shippingRegularArea['regular']['children']['referencia'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.referencia_envio');
        $walker->setValue('{SHIPPING_ADDRESS}.>>', $shippingRegularArea);
        $shippingFastArea = $walker->getValue('{SHIPPING_ADDRESS}.>>');
        $shippingFastArea['fast'] = $shippingFast;
        $shippingFastArea['fast']['children']['distrito'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.distrito_envio_rapido');
        $shippingFastArea['fast']['children']['distrito']['config']['options'] = [
            ['label' => "La Molina", 'value' => 'LA MOLINA'],
            ['label' => "Los Olivos", 'value' => 'LOS OLIVOS'],
            ['label' => "San Isidro", 'value' => 'SAN ISIDRO'],
            ['label' => "Jesús María", 'value' => 'JESUS MARIA'],
            ['label' => "Pueblo Libre", 'value' => 'PUEBLO LIBRE'],
            ['label' => "Magdalena del Mar", 'value' => 'MAGDALENA DEL MAR'],
            ['label' => "San Miguel", 'value' => 'SAN MIGUEL'],
            ['label' => "San Borja", 'value' => 'SAN BORJA'],
            ['label' => "Barranco", 'value' => 'BARRANCO '],
            ['label' => "Santiago de surco", 'value' => 'SANTIAGO DE SURCO '],
            ['label' => "Chorrillos", 'value' => 'CHORILLOS'],
            ['label' => "Surquillo", 'value' => 'SURQUILLO'],
            ['label' => "Miraflores", 'value' => 'MIRAFLORES']
        ];
        $shippingFastArea['fast']['children']['distrito']['config']['caption'] = "Selecciona un distrito...";
        $shippingFastArea['fast']['children']['direccion'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.street');
        $shippingFastArea['fast']['children']['referencia'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.referencia_envio');
        $walker->setValue('{SHIPPING_ADDRESS}.>>', $shippingFastArea);
        //Shipping Step fast shipping schedule
        $shippingFastScheduleComponent = [
            'component' => 'uiComponent',
            'displayArea' => 'schedule',
            'provider' => 'checkoutProvider'
        ];
        $shippingFastScheduleArea = $walker->getValue('{SHIPPING_ADDRESS}.>>');
        $shippingFastScheduleArea['schedule'] = $shippingFastScheduleComponent;
        $shippingFastScheduleArea['schedule']['children']['schedule'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.horarios_disponibles');
        $shippingFastScheduleArea['schedule']['children']['schedule']['component'] = "WolfSellers_Checkout/js/view/form/element/schedule";
        $shippingFastScheduleArea['schedule']['children']['schedule']['config']['elementTmpl'] = "WolfSellers_Checkout/form/element/schedule";
        $shippingFastScheduleArea['schedule']['children']['schedule']['label'] = "";
        $walker->setValue('{SHIPPING_ADDRESS}.>>', $shippingFastScheduleArea);
        //Shipping Step Summary Component
        $resumenShippingStep = [
            'component' => 'WolfSellers_Checkout/js/view/shipping-step-summary',
            'displayArea' => 'shipping-step-summary',
            'provider' => 'checkoutProvider'
        ];
        $shippingSummary = $walker->getValue('{CHECKOUT_STEPS}.>>');
        $shippingSummary['shipping-step-summary'] = $resumenShippingStep;
        $walker->setValue('{CHECKOUT_STEPS}.>>', $shippingSummary);
        //PickUp Step pickerComponent
        //'distrito-picker'
        $distritoPickupUiComponent = [
            'component' => 'uiComponent',
            'displayArea' => 'distrito-pickup',
            'provider' => 'checkoutProvider'
        ];
        $distritoPickupArea = $walker->getValue('{STORE-PICKUP}.>>');
        $distritoPickupArea['distrito-pickup'] = $distritoPickupUiComponent;
        $distritoPickupArea['distrito-pickup']['children']['distrito'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.distrito_pickup');
        $distritoPickupArea['distrito-pickup']['children']['distrito']['component'] = "WolfSellers_Checkout/js/view/form/element/distrito_pickup";
        $distritoPickupArea['distrito-pickup']['children']['distrito']['label'] = "Distrito";
        $distritoPickupArea['distrito-pickup']['children']['distrito']['config']['caption'] = "Seleccionar distrito";
        $distritoPickupArea['distrito-pickup']['children']['distrito']['config']['options'] = $this->_sourceHelper->getDistrictSource();
        $walker->setValue('{STORE-PICKUP}.>>',$distritoPickupArea);

        $pickerUiComponent = [
            'component' => 'uiComponent',
            'displayArea' => 'picker',
            'provider' => 'checkoutProvider'
        ];
        $pickerArea = $walker->getValue('{STORE-PICKUP}.>>');
        $pickerArea['picker'] = $pickerUiComponent;
        $pickerArea['picker']['children']['pickerOption'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.picker');
        $pickerArea['picker']['children']['pickerOption']['component'] = "WolfSellers_Checkout/js/view/form/element/picker";
        $pickerArea['picker']['children']['pickerOption']['config']['elementTmpl'] = "WolfSellers_Checkout/form/element/radio-btn";
        $pickerArea['picker']['children']['pickerOption']['label'] = " ";
        $walker->setValue('{STORE-PICKUP}.>>',$pickerArea);
        //another-picker
        $pickerUiComponent = [
            'component' =>  "WolfSellers_Checkout/js/view/anotherPickerForm",
            'displayArea' => 'another-picker',
            'provider' => 'checkoutProvider'
        ];
        $pickerArea = $walker->getValue('{STORE-PICKUP}.>>');
        $pickerArea['another-picker'] = $pickerUiComponent;
        $pickerArea['another-picker']['children']['identificacion_picker'] =
            $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.identificacion_picker');
        $pickerArea['another-picker']['children']['identificacion_picker']['config']['customScope'] = "anotherPicker.identificacion_picker";
        $pickerArea['another-picker']['children']['identificacion_picker']['dataScope'] = "anotherPicker.identificacion_picker";
        $pickerArea['another-picker']['children']['numero_identificacion_picker'] =
            $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.numero_identificacion_picker');
        $pickerArea['another-picker']['children']['numero_identificacion_picker']['config']['customScope'] = "anotherPicker.numero_identificacion_picker";
        $pickerArea['another-picker']['children']['numero_identificacion_picker']['dataScope'] = "anotherPicker.numero_identificacion_picker";
        $pickerArea['another-picker']['children']['nombre_completo_picker'] =
            $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.nombre_completo_picker');
        $pickerArea['another-picker']['children']['nombre_completo_picker']['config']['customScope'] = "anotherPicker.nombre_completo_picker";
        $pickerArea['another-picker']['children']['nombre_completo_picker']['dataScope'] = "anotherPicker.nombre_completo_picker";
        $pickerArea['another-picker']['children']['email_picker'] =
            $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.email_picker');
        $pickerArea['another-picker']['children']['email_picker']['config']['customScope'] = "anotherPicker.email_picker";
        $pickerArea['another-picker']['children']['email_picker']['dataScope'] = "anotherPicker.email_picker";
        $walker->setValue('{STORE-PICKUP}.>>',$pickerArea);
        //picker-voucher
        $voucherPickupUiComponent = [
            'component' => 'uiComponent',
            'displayArea' => 'picker-voucher',
            'provider' => 'checkoutProvider'
        ];
        $voucherPickupArea = $walker->getValue('{STORE-PICKUP}.>>');
        $voucherPickupArea['picker-voucher'] = $voucherPickupUiComponent;
        $distrito = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.colony');
        $voucherPickupArea['picker-voucher']['children']['voucher'] = $distrito;
        $voucherPickupArea['picker-voucher']['children']['voucher']['component'] = "WolfSellers_Checkout/js/view/form/element/voucher";
        $voucherPickupArea['picker-voucher']['children']['voucher']['caption'] = "Selecciona un distrito...";
        $voucherPickupArea['picker-voucher']['children']['direccion_comprobante_picker'] =
            $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.direccion_comprobante_picker');
        $voucherPickupArea['picker-voucher']['children']['direccion_comprobante_picker']["label"]  = "Dirección";
        $walker->setValue('{STORE-PICKUP}.>>',$voucherPickupArea);


        //Set displayArea to each step component
        $shippingStep = $walker->getValue('{SHIPPING-STEP}');
        $shippingStep['displayArea'] = "shipping-step";
        $walker->setValue('{SHIPPING-STEP}',$shippingStep);
        $storePickUpStep = $walker->getValue('{STORE-PICKUP-STEP}');
        $storePickUpStep['displayArea'] = "store-pickup-step";
        $walker->setValue('{STORE-PICKUP-STEP}',$storePickUpStep);
        $billingStep = $walker->getValue('{BILLING-STEP}');
        $billingStep['displayArea'] = "billing-step";
        $walker->setValue('{BILLING-STEP}',$billingStep);

        /******* BUTTON PLACE ORDER**********/

        $placeOrderComponent = [
          'component' => 'WolfSellers_Checkout/js/view/button-place-order',
          'displayArea' => 'button-place-order',
          'sortOrder' => 500
        ];
        $placeOrderFieldSets = [
              'component' => 'uiComponent',
              'displayArea' => 'summary-place-order',

        ];
        $placeOrderDataFieldSets = $walker->getValue('{SUMMARY}.>>');
        $placeOrderDataFieldSets['summary-place-order'] = $placeOrderFieldSets;
        $placeOrderDataFieldSets['summary-place-order']['children']['button-place-order']= $placeOrderComponent;
        $walker->setValue('{SUMMARY}.>>', $placeOrderDataFieldSets);

        /****** INVOICE REQUIRE FORM *****/

        $invoiceComponent = [
            'component' => 'WolfSellers_Checkout/js/view/invoice-checkout-form',
            'displayArea' => 'invoice-form',
            'provider' => 'checkoutProvider',
            'sortOrder' => '0'
        ];

        $invoiceFieldSets = [
            'component' => 'uiComponent',
            'provider' => 'checkoutProvider',
            'displayArea' => 'custom-checkout-form-fields'
        ];
        $invoiceDataFieldSets = $walker->getValue('{PAYMENT_FORM_INVOICE}.>>custom-checkout-form-fieldset.>>');
        $invoiceDataFieldSets['custom-checkout-form-fieldset'] = $invoiceFieldSets;
        $rucField = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'customCheckoutForm',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'customCheckoutForm.ruc',
            'label' => 'RUC',
            'sortOrder' => 30,
            'validation' => [
                'required-entry' => true,
            ],
        ];
        $razonField = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'customCheckoutForm',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'customCheckoutForm.razon_social',
            'label' => 'Razón Social',
            'sortOrder' => 20,
            'validation' => [
                'required-entry' => true,
            ],
        ];
        $fiscalField = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'customCheckoutForm',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',

            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'customCheckoutForm.direccion_fiscal',
            'label' => 'Dirección Fiscal',
            'sortOrder' => 40,
            'validation' => [
                'required-entry' => true,
            ],
        ];

        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required'] = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.picker');
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required']['component'] = 'WolfSellers_Checkout/js/view/form/element/payment-type';
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required']['config']['customScope'] = 'customCheckoutForm';
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required']['config']['elementTmpl'] = 'WolfSellers_Checkout/form/element/payment-type';
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required']['dataScope'] = 'customCheckoutForm.invoice_required';
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required']['label'] = '';
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required']['sortOrder'] = '10';
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['invoice_required']['validation'] = ['required-entry' => false];

        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['ruc'] = $rucField;
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['razon_social'] = $razonField;
        $invoiceDataFieldSets['custom-checkout-form-fieldset']['children']['direccion_fiscal'] = $fiscalField;

        $walker->setValue('{PAYMENT}.>>.beforeMethods.>>.invoice-form', $invoiceComponent);
        $walker->setValue('{PAYMENT}.>>.beforeMethods.>>.invoice-form.>>', $invoiceDataFieldSets);

        $razonSocial = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.razon_social');
        $razonSocial['visible'] = false;
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.razon_social', $razonSocial);

        $ruc = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.ruc');
        $ruc['visible'] = false;
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.ruc', $ruc);

        $direccionFiscal = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.direccion_fiscal');
        $direccionFiscal['visible'] = false;
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.direccion_fiscal', $direccionFiscal);

        /****** Componets payment continue *****/
        $paymentButtonFieldSets = [
            'component' => 'uiComponent',
            'displayArea' => 'payments-continue',
            'provider' => 'checkoutProvider',
        ];
        $paymentButtonComponent = [
            'component' => 'WolfSellers_Checkout/js/view/payment-continue',
            'displayArea' => 'payment-button',
            'config' => [
                'template' => 'WolfSellers_Checkout/payment-continue'
            ]
        ];
        /*********** Componets payment Agreement ***********************************************/
        $agreementsComponent = [
            'component' => 'WolfSellers_Checkout/js/view/payment-agrements'
        ];
        $paymentAgreementSets = [
            'component' => 'uiComponent',
            'displayArea' => 'payment-agreement',
            'provider' => 'checkoutProvider',
        ];
        /****** END INVOICE REQUIRE FORM *****/

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
            // add button continue payment

            $payment['children']['payment-agreement'] = $paymentAgreementSets;
            $payment['children']['payment-agreement'] = $agreementsComponent;
            $payment['children']['payments-continue'] = $paymentButtonFieldSets;
            $payment['children']['payments-continue'] = $paymentButtonComponent;

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

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getPaymentType()
    {
        $customField = [
            'component' => 'WolfSellers_Checkout/js/view/form/element/payment-type',
            'config' => [
                'customScope' => 'customCheckoutForm',
                'template' => 'ui/form/field',
                'elementTmpl' => 'WolfSellers_Checkout/form/element/payment-type',

            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'customCheckoutForm.invoice_required',
            'label' => '',
            'sortOrder' => 10,
            'validation' => [
                'required-entry' => false,
            ],
        ];

        return $customField;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomField($name,$scope)
    {
        $customField = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => $scope,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'tooltip' => [
                    "description" => 'Item information'
                ],
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => $scope.'.'.$name,
            'label' => '',
            'sortOrder' => '10',
            'validation' => [
                'required-entry' => true
            ],
        ];

        return $customField;
    }
}
