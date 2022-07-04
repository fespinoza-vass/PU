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

use Amasty\CheckoutCore\Block\Onepage\LayoutWalkerFactory;
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

        $company = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.company');
        $company['visible'] = false;
        $company['imports'] = [
            'visible' => '${ $.parentName }.invoice_required:value',
        ];
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.company', $company);

        $vat = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.vat_id');
        $vat['visible'] = false;
        $vat['imports'] = [
            'visible' => '${ $.parentName }.invoice_required:value',
        ];
        $vat['validation'] = array_merge($vat['validation'], [
            'required-entry' => true,
        ]);
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.vat_id', $vat);

        $city = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.city');
        $city['component'] = 'WolfSellers_Checkout/js/view/form/element/city';
        $city['validation'] = ['required-entry' => true];
        $city['config']['elementTmpl'] = 'ui/form/element/select';
        $city['filterBy'] = [
            'field' => 'region_id',
            'target' => '${ $.provider }:${ $.parentScope }.region_id',
        ];
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.city', $city);

        $colony = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.colony');
        $colony['component'] = 'WolfSellers_Checkout/js/view/form/element/colony';
        $colony['validation'] = ['required-entry' => true];
        $colony['config']['elementTmpl'] = 'ui/form/element/select';
        $colony['config']['options'] = [[
            'label' => "",
            'value' => '',
        ]];
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.colony', $colony);

        $fechaNacimiento = $walker->getValue('{SHIPPING_ADDRESS_FIELDSET}.>>.fecha_de_nacimiento');
        $fechaNacimiento['validation'] = [];
        $walker->setValue('{SHIPPING_ADDRESS_FIELDSET}.>>.fecha_de_nacimiento', $fechaNacimiento);

        $payments = $walker->getValue('{PAYMENT}.>>.payments-list');
        foreach ($payments["children"] as &$payment) {
            if(
                !empty($payment["children"])
                && !empty($payment["children"]["form-fields"])
                && !empty($payment["children"]["form-fields"]["children"])
                && !empty($payment["children"]["form-fields"]["children"]["fecha_de_nacimiento"])
            ) {
                $payment["children"]["form-fields"]["children"]["fecha_de_nacimiento"]["validation"] = [];
            }
        }

        $walker->setValue('{PAYMENT}.>>.payments-list', $payments);

        return $walker->getResult();
    }
}
