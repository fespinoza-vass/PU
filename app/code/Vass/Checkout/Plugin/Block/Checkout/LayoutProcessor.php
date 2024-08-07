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
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['visible']=true; 
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['colony']['visible']=true;
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
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['visible']=false; 
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
        
        
        return $jsLayout;
    }
}
