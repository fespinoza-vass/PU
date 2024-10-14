<?php

declare(strict_types=1);

namespace Vass\Checkout\Plugin\Block\Checkout;

use Amasty\CheckoutStyleSwitcher\Block\Onepage\BillingAddressRelocateProcessor;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class LayoutProcessor
{
    private array $hiddenFieldShipping = [
        'ruc',
        'razon_social',
        'direccion_fiscal',
        'company',
        'dni',
        'nombre_completo_picker',
        'email_picker',
        'direccion_comprobante_picker',
        'invoice_required',
        'distrito_envio_rapido',
        'horarios_disponibles',
        'comprobante_pickup',
        'distrito_pickup',
        'vat_id',
        'picker',
        'street',
        'region_id',
        'country_id',
        'postcode'
    ];

    private array $showFieldsBilling = [
        'ruc',
        'razon_social',
        'direccion_fiscal'
    ];

    private array $removeFieldsBilling = [
        'region_id',
        'numero_identificacion_picker'
    ];

    /**
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        private readonly Session $session,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function afterProcess(
        BillingAddressRelocateProcessor $subject,
        array $jsLayout
    ) {
        $idCustomer = $this->session->getCustomerId();

        $shippingAddressFields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children'];

        foreach ($shippingAddressFields as $field => $value) {
            if (in_array($field, $this->hiddenFieldShipping)) {
                $shippingAddressFields[$field]['visible'] = false;
            }
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['identificacion_picker'] = [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'id' => 'identificacion_picker',
                'options' => [
                    ["label" => __('DNI'), "value" => 868],
                    ["label" => __('Passport'), "value" => 865]
                ],
                'value' => $this->getIdentificacionCustomer($idCustomer),
            ],
            'validation' => [
                'required-entry' => true
            ],
            'dataScope' => 'shippingAddress.custom_attributes.identificacion_picker',
            'sortOrder' => 30,
            'label' => __('Document Type'),
            'provider' => 'checkoutProvider',
            'visible' => true,
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['numero_identificacion_picker'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'id' => 'numero_identificacion_picker',
                'sortOrder' => 35,
            ],
            'validation' => [
                'required-entry' => true
            ],
            'dataScope' => 'shippingAddress.custom_attributes.numero_identificacion_picker',
            'label' => __('Document number'),
            'provider' => 'checkoutProvider',
            'visible' => true,
            'value' => $this->getNumIdentificacionCustomer($idCustomer)
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['referencia_envio'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'id' => 'referencia_envio',
                'sortOrder' => 100
            ],
            'placeholder' => __('Ex: At block 34 of Benavides Avenue'),
            'dataScope' => 'shippingAddress.custom_attributes.referencia_envio',
            'label' => __('Shipping Reference'),
            'provider' => 'checkoutProvider',
            'visible' => true,
        ];

        $checkbox_privacy = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'checkout.sidebar.additional.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/checkbox',
                'id' => 'checkbox_privacidad',
            ],
            'dataScope' => 'checkout.sidebar.additional.checkbox_privacidad',
            'description' => '',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [
                'required-entry' => true,
            ],
            'sortOrder' => 10,
            'id' => 'custom-checkbox-privacidad',

        ];

        $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['checkboxes']
        ['children']['custom_checkbox2'] = $checkbox_privacy;

        $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['checkboxes']
        ['children']['gift_message_container']['sortOrder'] = 5;

        $jsLayout['components']['checkout']['children']['sidebar']['children']['additional']['children']['checkboxes']
        ['children']['subscribe']['sortOrder'] = 15;

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
            'sortOrder' => 60,
            'validation' => [
                'required-entry' => true
            ],
            'options' => [[
                'label' => 'Seleccione una Provincia',
                'value' => '',
                'disabled' => true,
                'selected' => true,
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
            'sortOrder' => 80,
            'validation' => [
                'required-entry' => true
            ],
            'options' => [[
                'label' => 'Seleccione un Distrito',
                'value' => '',
                'disabled' => true,
                'selected' => true,
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

        // Add city and colony fields to shipping address
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = $cityField;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['colony'] = $colonyField;

        // Preload customer data if is logged
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']
        ['value'] = $this->getNameCustomer($idCustomer);

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']
        ['value'] = $this->getLastNameCustomer($idCustomer);

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']
        ['value'] = $this->getTelefonoCustomer($idCustomer);

        $billingAddressFields = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['afterMethods']['children']['billing-address-form']['children']
        ['form-fields']['children'];

        if ($billingAddressFields) {
            foreach ($billingAddressFields as $field => $value) {
                if (!in_array($field, $this->showFieldsBilling)) {
                    $billingAddressFields[$field]['visible'] = false;

                    if (in_array($field, $this->removeFieldsBilling)) {
                        unset($billingAddressFields[$field]);
                    }
                }
            }
        }

        return $jsLayout;
    }

    /**
     * @param $idCustomer
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getNameCustomer($idCustomer)
    {
        $nameCustomer = "";
        if (isset($idCustomer)) {
            $customer = $this->customerRepository->getById($idCustomer);
            $nameCustomer = $customer->getFirstname();
        }
        return $nameCustomer;
    }

    /**
     * @param $idCustomer
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getLastNameCustomer($idCustomer)
    {
        $lastnameCustomer = "";
        if (isset($idCustomer)) {
            $customer = $this->customerRepository->getById($idCustomer);
            $lastnameCustomer = $customer->getLastname();
        }
        return $lastnameCustomer;
    }

    /**
     * @param $idCustomer
     * @return mixed|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getIdentificacionCustomer($idCustomer)
    {
        $typeCustomer = 'default';
        if (isset($idCustomer)) {
            $customer = $this->customerRepository->getById($idCustomer);
            $typeCustomer = $customer->getCustomAttribute('identificacion')->getValue();
        }
        return $typeCustomer;
    }

    /**
     * @param $idCustomer
     * @return mixed|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getNumIdentificacionCustomer($idCustomer)
    {
        $numIdCustomer = "";
        if (isset($idCustomer)) {
            $customer = $this->customerRepository->getById($idCustomer);
            $numIdCustomer = $customer->getCustomAttribute('numero_de_identificacion')->getValue();
        }
        return $numIdCustomer;
    }

    /**
     * @param $idCustomer
     * @return mixed|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getTelefonoCustomer($idCustomer)
    {
        $telCustomer = "";
        if (isset($idCustomer)) {
            $customer = $this->customerRepository->getById($idCustomer);
            $telCustomer = $customer->getCustomAttribute('telefono')->getValue();
        }
        return $telCustomer;
    }
}
