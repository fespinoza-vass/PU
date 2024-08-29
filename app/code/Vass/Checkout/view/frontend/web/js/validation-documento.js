require(['jquery', 'domReady!'], function($) {
    $(document).ready(function() {
        var tipoDocumentoSelector = 'select[name="custom_attributes[identificacion_picker]"]';
        var numeroIdentificacionInput = 'input[name="custom_attributes[numero_identificacion_picker]"]';

        // Añade un manejador de eventos para el cambio en el select
        $(document).on('change', tipoDocumentoSelector, function() {
            var selectedValue = $(this).val();
            var $input = $(numeroIdentificacionInput);

            // Limpia el campo de entrada y las validaciones y mensajes anteriores
            $input.val(''); // Limpia el campo de entrada
            $input.removeAttr('data-validate');
            $input.off('keyup'); // Desvincula el evento de keyup

            // Elimina el mensaje de validación personalizado y el borde rojo
            $input.siblings('.field-error').remove(); 
            $input.css('border-color', ''); // Elimina el color de borde

            if (selectedValue === '868') { // DNI
                console.log('Configurando validación para DNI');
                $input.attr('data-validate', JSON.stringify({
                    'required-entry': true,
                    'validate-length': { min: 8, max: 8 },
                    'validate-digits': true
                }));

                $input.on('keyup', function(event) {
                    var value = $(this).val().replace(/\D/g, ''); // Elimina caracteres no numéricos
                    $(this).val(value);
                    
                    // Validación de longitud para DNI
                    if (!/^\d{8}$/.test(value)) {
                        showValidationMessage('Por favor, ingrese 8 dígitos.');
                        $(this).css('border-color', '#ed8380'); // Agrega el color de borde en error
                    } else {
                        // Elimina el mensaje de validación si es válido
                        $input.siblings('.field-error').remove();
                        $(this).css('border-color', ''); // Elimina el color de borde si es válido
                    }

                    // Elimina el mensaje de campo obligatorio cuando se escribe
                    if (value.length > 0) {
                        $input.siblings('.field-error').filter('.field-required').remove();
                    }
                });
            } else if (selectedValue === '865') { // Pasaporte
                console.log('Configurando validación para Pasaporte');
                $input.attr('data-validate', JSON.stringify({
                    'required-entry': true,
                    'validate-length': { min: 6, max: 12 },
                    'validate-alphanum': true
                }));

                $input.on('keyup', function(event) {
                    var value = $(this).val().replace(/[^a-zA-Z0-9]/g, ''); // Elimina caracteres no alfanuméricos
                    $(this).val(value);
                    
                    // Validación de longitud para Pasaporte
                    if (!/^[a-zA-Z0-9]{6,12}$/.test(value)) {
                        showValidationMessage('Por favor, ingrese entre 6 y 12 caracteres.');
                        $(this).css('border-color', '#ed8380'); // Agrega el color de borde en error
                    } else {
                        // Elimina el mensaje de validación si es válido
                        $input.siblings('.field-error').remove();
                        $(this).css('border-color', ''); // Elimina el color de borde si es válido
                    }

                    // Elimina el mensaje de campo obligatorio cuando se escribe
                    if (value.length > 0) {
                        $input.siblings('.field-error').filter('.field-required').remove();
                    }
                });
            } else {
                console.log('Configurando validación para otro tipo de documento o sin validación específica');
                $input.off('keyup'); // Desvincula el evento de keyup
            }

            // Limpia el error nativo y el mensaje personalizado cuando se cambia la opción en el select
            $input.siblings('.field-error').remove(); // Elimina el mensaje de validación personalizado
            $input.css('border-color', ''); // Elimina el color de borde
            personalizeNativeErrorMessage($input);

            // Llamada manualmente a la validación de Magento 2 para actualizar el estado
            $input.trigger('change');
        });

        function showValidationMessage(message) {
            var $input = $(numeroIdentificacionInput);
            // Elimina cualquier mensaje de validación previo
            $input.siblings('.field-error').remove();
            // Agrega el mensaje personalizado
            $input.after('<div class="field-error">' + message + '</div>');
        }

        function personalizeNativeErrorMessage($input) {
            // Selecciona el div del mensaje de error nativo basado en `data-bind` del input específico
            var errorDiv = $input.siblings().filter(function() {
                return $(this).attr('data-bind') && $(this).attr('data-bind').includes('element.errorId');
            });

            if (errorDiv.length) {
                // Cambia el texto del mensaje de error
                errorDiv.find('span').text(''); // Elimina el mensaje de error nativo
                errorDiv.css('color', ''); // Elimina el color del mensaje de error nativo
            }
        }

        // Personalizar el mensaje de error nativo al perder el foco
        $(document).on('blur', numeroIdentificacionInput, function() {
            var $input = $(this);
            // Selecciona el div del mensaje de error nativo basado en `data-bind` del input específico
            var errorDiv = $input.siblings().filter(function() {
                return $(this).attr('data-bind') && $(this).attr('data-bind').includes('element.errorId');
            });

            if (errorDiv.length) {
                // Elimina el mensaje de error nativo
                errorDiv.find('span').text('');
                errorDiv.css('color', ''); // Elimina el color del mensaje de error nativo
            }

            // Mostrar mensaje de campo obligatorio si el campo está vacío
            if (!$input.val()) {
                showValidationMessage('Este es un campo obligatorio.');
            }
        });

        // Llamada inicial para establecer el estado correcto al cargar la página
        $(tipoDocumentoSelector).trigger('change');
    });
});
