require([
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Ui/js/modal/confirm',
        'jquery/ui',
        'domReady!'
    ], function ($, modal, confirm) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'modal-bopis',
            buttons: []
        };
        if(!$(".btn-preparar").prop("disabled")) {

            var p = modal(options, $('#popup-modal-preparar'));
            jQuery(".btn-preparar").on("click",function () {
                console.log("entro");
                $("#popup-modal-preparar").modal('openModal');
            });
            $("#popup-modal-preparar").find(".bopis-popup-btn-regresar").on("click", function (){
                $("#popup-modal-preparar").modal('closeModal');
            });
        }

        if(!$(".btn-enviar").prop("disabled")) {

            var p = modal(options, $('#popup-modal-enviar'));
            jQuery(".btn-enviar").on("click",function () {
                console.log("entro");
                $("#popup-modal-enviar").modal('openModal');
            });
            $("#popup-modal-enviar").find(".bopis-popup-btn-regresar").on("click", function (){
                $("#popup-modal-enviar").modal('closeModal');
            });
        }

        if(!jQuery(".btn-entregar").prop("disabled")) {

            var popupEntrega = modal(options, $('#popup-modal-entregar'));
            $(".btn-entregar").on("click", function () {
                $("#popup-modal-entregar").modal('openModal');
            });
            $("#popup-modal-entregar").find(".bopis-popup-btn-regresar").on("click", function (){
                $("#popup-modal-entregar").modal('closeModal');
            });
        }

        if(!$(".btn-detener").prop("disabled")) {

            var popupEntrega = modal(options, $('#popup-modal-hold'));
            $(".btn-detener").on("click", function () {
                $("#popup-modal-hold").modal('openModal');
            });
            $("#popup-modal-hold").find(".bopis-popup-btn-regresar").on("click", function (){
                $("#popup-modal-hold").modal('closeModal');
            });
        }

        var $confirmacionClienteRetira = $(".btn-confirmacion-cliente-retira");
        var $confirmacionClienteFactura = $(".btn-confirmacion-cliente-factura");
        var $confirmacionOrden = $(".btn-confirmacion-orden");
        var $confirmacionMetodoPago = $(".btn-confirmacion-cliente-metodo-pago");


        if($(".btn-confirmacion-cliente").length > 0) {
            $(".btn-confirmacion-cliente").on("click", function () {
                var $button = $(this);

                $("#bopis-confirmation-type-input").val($button.data("type"));
                $("#bopis-confirm-verification").submit();
            });
        }

        if($(".btn-cancelar").length > 0) {

            $(".btn-cancelar").on("click", function () {
                confirm({
                    content: "¿Deseas cancelar la orden?",
                    actions: {
                        /**
                         * Confirm action.
                         */
                        confirm: function () {
                            $("#bopis-cancelar-verification").submit();
                        }
                    }
                });
            });
        }
    }
);
