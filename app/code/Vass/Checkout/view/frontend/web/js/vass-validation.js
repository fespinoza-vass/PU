require([
    'jquery',
    'uiComponent'
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

        // Función para validar el número de documento
        function validarNumeroDocumento(tipo, numero) {
            if (tipo === 'DNI') {
                return /^\d{8}$/.test(numero); // 8 números
            } else if (tipo === 'Pasaporte') {
                return /^[a-zA-Z0-9]{6,12}$/.test(numero); // 6-12 caracteres alfanuméricos
            }
            return false;
        }

        // Aplicar la lógica de validación al campo de número de documento
        setTimeout(function() {
            var $selectTipoDocumento = $('[name="custom_attributes[identificacion_picker]"]');
            var $inputNumeroDocumento = $('[name="custom_attributes[numero_identificacion_picker]"]');

            // Cambiar el placeholder y la validación según el tipo de documento
            $selectTipoDocumento.on('change', function() {
                var tipoDocumento = $(this).val();
                
                if (tipoDocumento === 'DNI') {
                    $inputNumeroDocumento.attr('placeholder', 'Ingrese su DNI');
                    $inputNumeroDocumento.attr('maxlength', '8');
                } else if (tipoDocumento === 'Pasaporte') {
                    $inputNumeroDocumento.attr('placeholder', 'Ingrese su Pasaporte');
                    $inputNumeroDocumento.attr('maxlength', '12');
                } else {
                    $inputNumeroDocumento.attr('placeholder', 'Ingrese su número de documento');
                    $inputNumeroDocumento.removeAttr('maxlength');
                }

                // Limpiar el valor del input
                $inputNumeroDocumento.val('');
            });

            // Validar el número de documento al salir del campo
            $inputNumeroDocumento.on('blur', function() {
                var tipoDocumento = $selectTipoDocumento.val();
                var numeroDocumento = $(this).val();

                if (!validarNumeroDocumento(tipoDocumento, numeroDocumento)) {
                    alert('El número de documento ingresado no es válido.');
                    $(this).val('');
                }
            });

            // Configurar un observador para asegurar que los campos se cargan dinámicamente
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Es un nodo de elemento
                            if ($(node).find('[name="custom_attributes[identificacion_picker]"]').length ||
                                $(node).is('[name="custom_attributes[identificacion_picker]"]')) {
                                $selectTipoDocumento = $('[name="custom_attributes[identificacion_picker]"]');
                                $inputNumeroDocumento = $('[name="custom_attributes[numero_identificacion_picker]"]');
                            }
                        }
                    });
                });
            });

            // Observar el body para cambios
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }, 500); // Retraso para esperar a que se carguen los componentes

        $('input[name="country_id"]').closest('.field').hide();
    });
});
