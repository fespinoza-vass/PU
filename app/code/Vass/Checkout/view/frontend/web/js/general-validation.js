require([
    'jquery',
    'uiComponent',
], function($, Component) {
    'use strict';

    console.log('Archivo de inicialización personalizado cargado.');

    $(document).ready(function () {
        // Función para aplicar el placeholder y la validación de solo letras
        function addPlaceholderAndValidation(inputName, placeholderText) {
            var $input = $('input[name="' + inputName + '"]');
            if ($input.length) {
                $input.attr('placeholder', placeholderText);
                console.log('Placeholder "' + placeholderText + '" aplicado en el campo "' + inputName + '".');

                // Validación de solo letras y longitud máxima
                $input.on('input', function() {
                    let value = $(this).val();
                    let isValid = /^[a-zA-Z\s]*$/.test(value);
                    let maxLength = 50;

                    // Si el valor no es válido o supera la longitud máxima
                    if (!isValid || value.length > maxLength) {
                        // Eliminar caracteres no permitidos y limitar a la longitud máxima
                        $(this).val(value.replace(/[^a-zA-Z\s]/g, '').substring(0, maxLength));
                    }
                });

                return true; // Placeholder y validación aplicados
            }
            return false; 
        }

        // Intentar aplicar el placeholder y la validación para firstname y lastname
        setTimeout(function() {
            if (!addPlaceholderAndValidation('firstname', 'Ej: Christopher')) {
                console.log('Campo "firstname" no encontrado inicialmente, configurando observador.');
            }
            if (!addPlaceholderAndValidation('lastname', 'Ej: Bueno')) {
                console.log('Campo "lastname" no encontrado inicialmente, configurando observador.');
            }

            // Configurar un observador si los campos no están presentes
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Es un nodo de elemento
                            if ($(node).find('input[name="firstname"]').length || $(node).is('input[name="firstname"]')) {
                                if (addPlaceholderAndValidation('firstname', 'Ej: Christopher')) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "firstname".');
                                }
                            }
                            if ($(node).find('input[name="lastname"]').length || $(node).is('input[name="lastname"]')) {
                                if (addPlaceholderAndValidation('lastname', 'Ej: Bueno')) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "lastname".');
                                }
                            }
                        }
                    });
                });
            });

            // Configurar el observador para observar cambios en el body
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }, 500); // Retraso de 500ms para permitir la carga de componentes dinámicos

        $('input[name="country_id"]').closest('.field').hide();
    });
});
