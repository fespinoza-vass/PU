require([
    'jquery',
    'mage/translate',
    'domReady!'
], function($, $t) {
    $(document).ready(function() {
        const OPTIONS = {
            DNI: '868',
            PASSPORT: '865'
        }

        let load = false;
        let documentSelector = 'select[name="custom_attributes[identificacion_picker]"]';
        let numberDocumentInput = 'input[name="custom_attributes[numero_identificacion_picker]"]';

        $(document).on('change', documentSelector, function(event) {
            let self = this;
            let selectedValue = $(this).val();
            let $input = $(numberDocumentInput);
            let $select = $(documentSelector);
            let loader = $('.loading-mask').length;

            let referenceAddress = $('textarea[name="custom_attributes[referencia_envio]"]');
            if (referenceAddress.val() === 'referencia_envio') {
                referenceAddress.val('');
            }

            if (!loader) $input.val('');
            $input.removeAttr('data-validate');
            $input.off('keyup');
            $input.parent().parent().addClass('_error');
            $input.siblings('.field-error').remove();
            if (!$input.siblings('.field-error').length) {
                showValidationMessage($t('This is a required field.'));
            }

            if (selectedValue === OPTIONS.DNI) {
                $input.off('keyup');
                $select.parent().parent().removeClass('_error');
                $select.siblings('.field-error').remove();
                if (!loader) $input.val('');
                $input.attr('maxlength', 8);
                $input.attr('data-validate', JSON.stringify({
                    'required-entry': true,
                    'validate-length': { min: 8, max: 8 },
                    'validate-digits': true
                }));

                $input.on('keyup', function(event) {
                    validateDNI(event, $(this));
                });
            } else if (selectedValue === OPTIONS.PASSPORT) {
                $input.off('keyup');
                $select.parent().parent().removeClass('_error');
                $select.siblings('.field-error').remove();
                if (!loader) $input.val('');
                $input.attr('maxlength', 12);
                $input.attr('data-validate', JSON.stringify({
                    'required-entry': true,
                    'validate-length': { min: 6, max: 12 },
                    'validate-alphanum': true
                }));

                $input.on('keyup', function(event) {
                    validatePassport(event, $(this));
                });
            } else {
                $select.val(OPTIONS.DNI);
                $select.siblings('.field-error').remove();
                $input.removeAttr('maxlength');
                $input.siblings('.field-error').remove();
                $input.attr('maxlength', 8);
                if (!loader) $input.val('');
                $input.val($input.val().replace('numero_identificacion_picker', ''));
                validateDNI(event, $input);

                setTimeout(function() {
                    if (!$input.siblings('.field-error').length && !$input.val()) {
                        showValidationMessage($t('This is a required field.'));
                        $input.parent().parent().addClass('_error');
                        $input.focus();
                    }
                }, 1000);

                $input.on('keyup', function(event) {
                    validateDNI(event, $(this));
                });
            }
        });

        function showValidationMessage(message, input = true) {
            let $input = null;
            if (input) {
                $input = $(numberDocumentInput);
            } else {
                $input = $(documentSelector);
            }

            let id = $input.attr('id');
            $input.siblings('.field-error').remove();
            $input.after(`<div class="field-error" id="error-${id}">${message}</div>`);
        }

        function validatePassport(event, input) {
            let value = input.val().replace(/[^a-zA-Z0-9]/g, '');
            input.val(value);

            if (!/^[a-zA-Z0-9]{6,12}$/.test(value)) {
                showValidationMessage($t('Please enter between %1 and %2 symbols.')
                    .replace('%1', 6).replace('%2', 12));
                input.parent().parent().addClass('_error');
            } else {
                input.siblings('.field-error').remove();
                input.parent().parent().removeClass('_error');
            }
        }

        function validateDNI(event, input) {
            let value = input.val().replace(/\D/g, '');
            input.val(value);

            if (!/^\d{8}$/.test(value)) {
                showValidationMessage($t('Please enter less or equal than %1 symbols.').replace('%1', 8));
                input.parent().parent().addClass('_error');
            } else {
                input.siblings('.field-error').remove();
                input.parent().parent().removeClass('_error');
            }
        }

        $(document).on('blur', numberDocumentInput, function() {
            let $input = $(this);
            let errorDiv = $input.siblings().filter(function() {
                return $(this).attr('id') && $(this).attr('id').includes('error');
            });

            if (errorDiv.length) {
                errorDiv.find('span').text('');
                $input.parent().parent().addClass('_error');
            }

            if (!$input.val()) {
                $input.parent().parent().addClass('_error');
                showValidationMessage($t('This is a required field.'));
            }
        });

        $(documentSelector).trigger('change');
    });
});
