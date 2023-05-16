/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/select',
    'WolfSellers_Checkout/js/model/address/ubigeo'
], function (Select, ubigeo) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                regionId: '${ $.parentName }.region_id:value',
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
            ubigeo.getUbigeos(this.regionId, value);
            return this._super();
        }
    });
});

