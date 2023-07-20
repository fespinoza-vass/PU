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

/**
 * Onepage Layout Processor.
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /** @var LayoutWalkerFactory */
    private LayoutWalkerFactory $walkerFactory;

    /**
     * @param LayoutWalkerFactory $walkerFactory
     */
    public function __construct(LayoutWalkerFactory $walkerFactory)
    {
        $this->walkerFactory = $walkerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout): array
    {
        $walker = $this->walkerFactory->create(['layoutArray' => $jsLayout]);
        //CUSTOMER DATA AREA
        $customerDataComponent = [
            'component' => 'WolfSellers_Checkout/js/view/customer-data-step',
            'displayArea' => 'customer-data-step',
            'sortOrder' => '0'
        ];
        $customerAddressArea = $customerDataComponent;
        $walker->setValue('{CHECKOUT_STEPS}.>>.customer-data-step', $customerAddressArea);
        //customer-fieldsets
        //Customer Data Nombre
        $customerDataNombreComponent = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'displayArea' => 'customer-data-nombre',
            'config' => [
                'customScope' => 'customerData.name',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'dataScope' => 'customerData.name',
            'label' => 'Customer Name',
            'provider' => 'checkoutProvider',
            'sortOrder' => 1,
            'validation' => [
                'required-entry' => false
            ],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
        ];
        $customerFieldsets = [
          'component' => 'uiComponent',
          'displayArea' => 'customer-fieldsets'
        ];
        $customerDataFieldSets = $walker->getValue('{CUSTOMER-DATA}.>>');
        $customerDataFieldSets['customer-fieldsets'] = $customerFieldsets;
        $customerDataFieldSets['customer-fieldsets']['children']['customer-data-name'] = $customerDataNombreComponent;
        $customerDataFieldSets['customer-email'] = $walker->getValue('{SHIPPING_ADDRESS}.>>.customer-email');
        $walker->setValue('{CUSTOMER-DATA}.>>', $customerDataFieldSets);
        $walker->setValue('{SHIPPING_ADDRESS}.>>.customer-email', []);

        //var_dump($customerAddressArea);
        //die();
        //
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
}
