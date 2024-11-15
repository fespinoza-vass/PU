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
        initialize: function () {
            this._super();
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
            var shipping = registry.get("checkout.steps.shipping-step.shippingAddress");
            if(_.isUndefined(shipping)){
                return;
            }
            if (_.isUndefined(shipping.isFastShipping())){
                return;
            }
            var region_id = registry.get("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.region_id");

            if (!_.isUndefined(region_id) && !_.isUndefined(region_id.value())) {
                ubigeo.getUbigeos(region_id.value(), value);
            }
            if(_.isEmpty(value)){
                return;
            }
            return this._super();
        }
    });
});

