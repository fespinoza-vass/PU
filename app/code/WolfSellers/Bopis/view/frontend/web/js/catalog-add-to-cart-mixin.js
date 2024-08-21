define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    var catalogAddToCartMixin = {
        options: {
            errorMessage: "Agrega una dirección de entrega o una Sucursal para recoger tu producto",
            messagesSelector: '[data-placeholder="messages"]',
        },

        submitForm: function (form) {
            /*var bopis = customerData.get('bopis');
            if (bopis().is_active){
                if (bopis().error){
                    $(this.options.messagesSelector).html("<div class='message-error error message'>" + this.options.errorMessage + "</div>");
                    return ;
                }
                if (bopis().cart_have_bundle){
                }
            }
*/
            return this._super(form);
        }
    };

    return function (targetWidget) {
        $.widget('mage.catalogAddToCart', targetWidget, catalogAddToCartMixin);

        return $.mage.catalogAddToCart;
    };
});
