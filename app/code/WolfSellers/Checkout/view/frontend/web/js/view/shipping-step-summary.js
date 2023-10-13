define([
    'ko',
    'underscore',
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function (
    ko,
    _,
    Component,
    quote
) {
    'use strict';

    return Component.extend({
        defaults:{
            template:'WolfSellers_Checkout/shipping-step-summary'
        },
        isShipping: ko.observable(true),
        isStorePickUp: ko.observable(false),
        initialize:function () {
            this._super();
            quote.shippingMethod.subscribe(function (value) {
                if (_.isUndefined(value) && _.isUndefined(value.carrier_code)){
                    console.log("undefined");
                    return;
                }
                if (value.carrier_code.includes('instore')){
                    this.isShipping(false);
                    this.isStorePickUp(true);
                }else{
                    this.isShipping(true);
                    this.isStorePickUp(false);
                }
            },this);
           return this;
        },
        /**
         * Add text value for reference input
         * @returns {*|boolean|string}
         */
        getReferencia: function () {
            if (!_.isUndefined(this.getCustomAttributeByAttributeCode('referencia_envio'))){
                return this.getCustomAttributeByAttributeCode('referencia_envio');
            }
            return "Valor de referencia no obtenido";
        },
        /**
         * Add text value for store pickup selected input
         * @returns {string}
         */
        getStorePickUpSelected: function () {
            return "Tienda Seleccionada.-.,-.,-.,-.,-.,";
        },
        /**
         * Gets a custom attribute by attribute code
         * TODO validate if is customAttributes null or empty
         * Warning this could be broke checkout until is finished
         * @param attributeCode
         * @returns {*|boolean}
         */
        getCustomAttributeByAttributeCode: function (attributeCode) {
            if (_.isUndefined(quote.shippingAddress().customAttributes) ||
                _.isObject(quote.shippingAddress().customAttributes) ||
                !_.isEmpty(quote.shippingAddress().customAttributes) ){
                var result = _.find(quote.shippingAddress().customAttributes,
                    {'attribute_code':attributeCode});
                if (result){
                    return result.value;
                }
            }
            return false;
        }
    });
});
