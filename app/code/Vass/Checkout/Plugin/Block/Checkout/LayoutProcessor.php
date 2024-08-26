<?php

declare(strict_types=1);

namespace Vass\Checkout\Plugin\Block\Checkout;

class LayoutProcessor
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
        
    ) {
        
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['ruc']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['razon_social']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['direccion_fiscal']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['referencia_envio']['visible']= true;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['dni']['visible']=false;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['numero_identificacion_picker']['visible'] = true;
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
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['visible']=false; 
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['observable']=false; 
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['visible'] =false; 

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['visible']=false;
            
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['visible'] = true; 

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
                    ['value' => '12552', 'label' => __('DNI')],
                    ['value' => '12555', 'label' => __('Pasaporte')]
                ],
                'validation' => [
                    'required-entry' => true
                ],
            ],
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
        'description' => 'He leído y acepto los',
        'provider' => 'checkoutProvider',
        'visible' => true,
        'validation' => [
            'required-entry' => false,
        ],
        'sortOrder' => 100,
        
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
        'description' => 'He leído y acepto la', // Cambia esto a la etiqueta deseada
        'provider' => 'checkoutProvider',
        'visible' => true,
        'validation' => [
            'required-entry' => true, // Cambia a true si deseas que sea obligatorio
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
        'description' => 'Acepto Política de Envío de', // Cambia esto a la etiqueta deseada
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

        return $jsLayout;

        
    }
   
    
}
