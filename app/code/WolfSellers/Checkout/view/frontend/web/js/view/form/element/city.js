/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/select',
    'WolfSellers_Checkout/js/model/address/ubigeo',
    'uiRegistry',
    'underscore'
], function (Select, ubigeo, registry, _) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                initialOptions: 'index = checkoutProvider:dictionaries.city_id',
                setOptions: 'index = checkoutProvider:dictionaries.city_id'
            }
        },

        /**
         * @param value
         *
         * @returns {*}
         */
        onUpdate: function (value) {
            var region_id = registry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id");
            var ubigeo = ubigeo;
            if(_.isUndefined(ubigeo) && _.isEmpty(value)){
                return;
            }

            if (!_.isUndefined(region_id) && !_.isUndefined(region_id.value())) {
                ubigeo.getUbigeos(region_id.value(), value);
            }
            return this._super();
        }
    });
});

