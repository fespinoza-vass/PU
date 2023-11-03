/**
 * @api
 */
define([
    'underscore',
    'Magento_Ui/js/form/element/select',
    'WolfSellers_Checkout/js/model/address/ubigeo',
    'uiRegistry'
], function (_, Select, ubigeo, registry) {
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
            this.options.subscribe(function (value) {
                value = _.map(value, function (item) {
                    if(!_.isUndefined(item)){
                        if(item.label.length >= 3){
                            item.label = item.label.charAt(0).toUpperCase() + item.label.slice(1).toLowerCase();
                        }
                        return item;
                    }
                    return item;
                });
            }, this);
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
            if(_.isUndefined(ubigeo) && _.isEmpty(value)){
                return;
            }
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

