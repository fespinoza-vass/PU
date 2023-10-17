require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'domReady!'
],function(
    $,
    modal,
) {
    'use strict';
    var options = {
            modalSelector: '#cart-modal-products',
        },
        productModal;


    $(document).on('click', '.tocart' , function (event) {
        var idButtonAddProduct = "#" + this.id;
        event.preventDefault();

        //validacion logica de entrega rapida ::
//popup modal code
        var popup = $('<div class="add-to-cart-modal-popup"/>').html($('.page-title span').text() + '<span> has been added to cart.</span>').modal({
            modalClass: 'add-to-cart-popup',
            title: $.mage.__("Popup Title"),
            buttons: [
                {
                    text: $.mage.__("Editar Carrito"),
                    click: function () {
                        window.location = window.checkout.shoppingCartUrl
                        //this.closeModal();
                    }
                },
                {
                    text: $.mage.__("Continuar compra"),
                    click: function () {
                        window.location = window.checkout.checkoutUrl
                    }
                }
            ]
        });
        popup.modal('openModal');
        //}
        event.preventDefault();
    });

});






