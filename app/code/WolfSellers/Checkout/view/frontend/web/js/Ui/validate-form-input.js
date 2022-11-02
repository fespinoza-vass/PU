/**
 *
 */
define([
    'Magento_Ui/js/form/element/select',
    'jquery',
], function (Select,$) {
    'use strict';
    return Select.extend({
        initialize: function () {
            var self = this;
            self._super();
            self.value.subscribe(function(anotherValue){
                if (!anotherValue){
                    $("div[name='shippingAddress.company']").hide()
                    $("div[name='shippingAddress.custom_attributes.dni']").hide()
                }
                if (anotherValue){
                    $("div[name='shippingAddress.company']").show()
                    $("div[name='shippingAddress.custom_attributes.dni']").show()
                }
            }, this);
            self.clearInputs()
        },
        clearInputs:function () {
            var attr = self.checkoutConfig.shippingAddressFromData.custom_attributes;
            attr.dni = null;
            attr.referencia_envio = null;
        }
    });
});
