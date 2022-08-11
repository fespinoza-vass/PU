/**
 * @api
 */
define([
    'Magento_Ui/js/form/element/select',
    'WolfSellers_Checkout/js/model/address/ubigeo',
    'uiRegistry'
], function (Select, ubigeo, registry) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false
        },

        /**
         * @returns {*}
         */
        initialize: function () {
            this._super();

            var self = this;

            ubigeo.listUbigeo.subscribe(function (listUbigeo) {
                self.setOptions(listUbigeo);
            });
            this.onUpdate();
            return this;
        },

        /**
         * @param value
         *
         * @returns {*}
         */
        onUpdate: function (value) {
            var ubigeo = ubigeo;
            var optSelected;

            if (typeof this.getOption !== 'function') {
                return;
            }

            if (value && (optSelected = this.getOption(value))) {
                ubigeo = optSelected.postcode;
            }

            registry.get(this.parentName + '.' + 'postcode', function (postcodeField) {
                postcodeField.value(ubigeo);
            }.bind(this));

            return this._super();
        }
    });
});

