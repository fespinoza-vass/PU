require([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function($, modal) {
    'use strict';

    $(document).ready(function() {
        var popupOpened = false;
        var popupInstance;

        // Función para construir la URL usando window.location
        function getUrl(path) {
            return window.location.origin + path; // Construye la URL base + path
        }

        function openPopup(pageUrl, popupTitle) {
            if (popupOpened) {
                return; // Evita abrir el popup si ya está abierto
            }

            popupOpened = true; // Marca el popup como abierto

            $('.header.content').css({
                'display': 'none'
            });

            $.ajax({
                url: pageUrl,
                type: 'GET',
                success: function(response) {
                    var $content = $('<div>').html(response);
                    var content = $content.find('main').html() || $content.html();

                    if (!content) {
                        // Maneja el caso en que no hay contenido
                        return;
                    }

                    var popupOptions = {
                        type: 'popup',
                        responsive: true,
                        innerScroll: true,
                        title: popupTitle,
                        buttons: [{
                            text: $.mage.__('Cerrar'),
                            class: 'action-secondary modal-close',
                            click: function() {
                                this.closeModal();
                            }
                        }],
                        closeOnEscape: true,
                        closed: function() {
                            popupOpened = false;

                            $('.header.content').css({
                                'display': ''
                            });
                        }
                    };

                    // Cierra el popup anterior si existe
                    if (popupInstance) {
                        popupInstance.closeModal();
                    }

                    // Inicializa el popup
                    popupInstance = modal(popupOptions, $('<div>').html(content));
                    popupInstance.openModal();

                    // Oculta la clase .modal-title después de abrir el popup
                    $('.modal-popup .modal-title').hide();

                    $('.modal-popup').prepend('<button class="action-close" type="button" title="Cerrar"><span aria-hidden="true">×</span></button>');

                    $('.modal-popup .modal-close').css({
                        'color': '#727272',
                        'text-align': 'right',
                        'font-family': '"Open Sans", sans-serif',
                        'font-size': '20px',
                        'font-style': 'normal',
                        'font-weight': '600',
                        'line-height': 'normal',
                        'margin-top': '-30px'
                    });

                    $('.modal-popup').addClass('custom-popup-style');
                },
                error: function(xhr, status, error) {
                    popupOpened = false;

                    $('.header.content').css({
                        'display': ''
                    });
                }
            });
        }

        // Configura los eventos utilizando getUrl para construir la URL
        $(document).on('click', '#tyc', function(event) {
            event.stopPropagation();
            event.preventDefault();
            openPopup(getUrl('/popup-tyc'), 'Términos y Condiciones');
        });

        $(document).on('click', '#privacidad', function(event) {
            event.stopPropagation();
            event.preventDefault();
            openPopup(getUrl('/privacidad-datos-personales'), 'Política de Protección de Datos Personales');
        });

        $(document).on('click', '#comunicaciones', function(event) {
            event.stopPropagation();
            event.preventDefault();
            openPopup(getUrl('/politicas-de-publicidad'), 'Comunicaciones de Publicidad y Promociones');
        });
    });
});
