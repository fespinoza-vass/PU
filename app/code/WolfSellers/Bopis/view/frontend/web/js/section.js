define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'ko',
    'jquery',
    'domReady!'
], function (Component, customerData,ko, $) {
    'use strict';

    return Component.extend({
        options : {
            tabDeliverySelector: '#tab-delivery',
            tabStorePickupSelector: '#tab-store-pickup',
            tabStorePickup: '.tab-store-pickup',
            tabDelivery: '.tab-delivery',
            addButton: '#product-addtocart-button'
        },

        initialize: function () {
            var self = this;
            this._super();
            customerData.reload(['bopis'], false)
            this.bopis = customerData.get('bopis');
            this.bopis.subscribe(function (value){
                console.log('section - subscribe bopis');
                console.log(value);
                $(".btn-lnk").attr("style", "")
                if (value.error) $(this.options.tabDelivery).trigger("click");
                if (value.type === 'delivery'){
                    self.updateAddressData();
                    localStorage.setItem('bopis', 0);
                }
                if (value.type === 'store-pickup'){
                    self.updateSourceData();
                    localStorage.setItem('bopis', 1);
                }

            }, this);
        },

        updateAddressData  : function () {

            var addressData = this.bopis().formatted;

            if (addressData === null || addressData === undefined) {
                return;
            }

            var content = $('<div>').append($('<span>').addClass('shipping-address').append(addressData)
            ).append($('<a>').addClass('shipping-address').attr('href', 'javascript:void(0)').append('Cambiar dirección de envío')
            );

            $(this.options.tabDeliverySelector).empty().append(content);
            $(this.options.tabDelivery).trigger("click")

            content = $('<a>').addClass('select-source').attr('href', 'javascript:void(0)').append('Seleccionar tienda');

            $(this.options.tabStorePickupSelector).empty().append(content);
            $(this.options.addButton).attr("type", "submit");
            $(this.options.addButton).children("span").text("Agregar al carrito");
        },

        updateSourceData: function (){
            var source = customerData.get('bopis');

            var sourceData = source().object;

            if (sourceData === null || sourceData === undefined) {
                return;
            }

            var content = $('<div>').append($('<span>').addClass('store-selected').append(source().formatted)
            ).append($('<a>').addClass('select-source').attr('href', 'javascript:void(0)').append('Cambiar tienda'));

            $(this.options.tabStorePickupSelector).empty().append(content);
            $(this.options.tabStorePickup).trigger("click")

            content = $('<a>').addClass('shipping-address').attr('href', 'javascript:void(0)').append('Ingresar dirección de envío');

            $(this.options.tabDeliverySelector).empty().append(content);

            if (source().can_buy){
                $(this.options.addButton).children("span").text("Agregar al carrito");
                $(this.options.addButton).attr("type", "submit");
            }else{
                $(this.options.addButton).attr("type", "button");
                $(this.options.addButton).children("span").text("No disponible en la tienda seleccionada");
            }
        }
    });
});
