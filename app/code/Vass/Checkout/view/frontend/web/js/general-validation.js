require([
    'jquery',
    'uiComponent',
], function($, Component) {
    'use strict';

    $(document).ready(function () {
        $('.checkout-payment-method').css('display', 'none');

        // Función para aplicar el placeholder y la validación de solo letras
        function addPlaceholderAndValidation(inputName, placeholderText, validationPattern) {
            var $input = $('input[name="' + inputName + '"]');
            if ($input.length) {
                $input.attr('placeholder', placeholderText);
                console.log('Placeholder "' + placeholderText + '" aplicado en el campo "' + inputName + '".');

                // Validación de solo letras y longitud máxima si el patrón de validación es proporcionado
                if (validationPattern) {
                    $input.on('input', function() {
                        let value = $(this).val();
                        let isValid = validationPattern.test(value);
                        let maxLength = 50;

                        // Si el valor no es válido o supera la longitud máxima
                        if (!isValid || value.length > maxLength) {
                            // Eliminar caracteres no permitidos y limitar a la longitud máxima
                            $(this).val(value.replace(validationPattern, '').substring(0, maxLength));
                        }
                    });
                }

                return true; 
            }
            return false; 
        }

        // Función para agregar una opción tipo placeholder a un select
        function addSelectPlaceholder(selectName, placeholderText) {
            var $select = $('select[name="' + selectName + '"]');
            if ($select.length && !$select.find('option[value=""]').length) {
                $select.prepend('<option value="" disabled selected hidden>' + placeholderText + '</option>');
                console.log('Placeholder "' + placeholderText + '" aplicado en el select "' + selectName + '".');
                return true;
            }
            return false;
        }

        // Aplicar los placeholders y validaciones necesarios
        setTimeout(function() {
            addPlaceholderAndValidation('firstname', 'Ej: Christopher', /^[a-zA-Z\s]*$/);
            addPlaceholderAndValidation('lastname', 'Ej: Bueno', /^[a-zA-Z\s]*$/);
            addPlaceholderAndValidation('custom_attributes[numero_identificacion_picker]', 'Ej: 121345678');
            addSelectPlaceholder('custom_attributes[colony]', 'Seleccione distrito');
            addSelectPlaceholder('custom_attributes[city]', 'Seleccione una provincia'); // Ajuste para el select de ciudad
            addPlaceholderAndValidation('custom_attributes[referencia_envio]', 'Ej: Casa de esquina');

            // Configuración del observador para detectar cambios en el DOM
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Es un nodo de elemento
                            if ($(node).find('input[name="firstname"]').length || $(node).is('input[name="firstname"]')) {
                                if (addPlaceholderAndValidation('firstname', 'Ej: Christopher', /^[a-zA-Z\s]*$/)) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "firstname".');
                                }
                            }
                            if ($(node).find('input[name="lastname"]').length || $(node).is('input[name="lastname"]')) {
                                if (addPlaceholderAndValidation('lastname', 'Ej: Bueno', /^[a-zA-Z\s]*$/)) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "lastname".');
                                }
                            }
                            if ($(node).find('input[name="custom_attributes[numero_identificacion_picker]"]').length || $(node).is('input[name="custom_attributes[numero_identificacion_picker]"]')) {
                                if (addPlaceholderAndValidation('custom_attributes[numero_identificacion_picker]', 'Ej: 121345678')) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "custom_attributes[numero_identificacion_picker]".');
                                }
                            }
                            if ($(node).find('select[name="custom_attributes[colony]"]').length || $(node).is('select[name="custom_attributes[colony]"]')) {
                                if (addSelectPlaceholder('custom_attributes[colony]', 'Seleccione distrito')) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "custom_attributes[colony]".');
                                }
                            }
                            if ($(node).find('select[name="custom_attributes[city]"]').length || $(node).is('select[name="custom_attributes[city]"]')) {
                                if (addSelectPlaceholder('custom_attributes[city]', 'Seleccione una provincia')) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "custom_attributes[city]".');
                                }
                            }
                            if ($(node).find('input[name="custom_attributes[referencia_envio]"]').length || $(node).is('input[name="custom_attributes[referencia_envio]"]')) {
                                if (addPlaceholderAndValidation('custom_attributes[referencia_envio]', 'Ej: Casa de esquina')) {
                                    console.log('Placeholder aplicado tras detectar cambios en el DOM para "custom_attributes[referencia_envio]".');
                                }
                            }
                        }
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }, 500); 

        $('input[name="country_id"]').closest('.field').hide();
    });
});
