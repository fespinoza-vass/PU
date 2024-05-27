define([
    'ko',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'underscore'
], function (ko, registry, Select,_) {
    'use strict';
    /**
     * Object literals
     * @type json Object {}
     */
    var inputConfiguration = {
        '11177': {
            placeholder: 'No. Pasaporte',
            validations: {"validate-identificacion-pasaporte":true,'required-entry':true,'min_text_length':'8','max_text_length':'8',"validate-number": true},
            inputType: "number"
        },
        '11174': {
            placeholder: 'DNI',
            validations: {"validate-identificacion-dni":true,'required-entry':true,'min_text_length':'8','max_text_length':'8',"validate-number": true},
            inputType: "number"
        },
        'default': {
            placeholder: "Selecciona Tipo Identificación",
            validations: {'required-entry':true,'min_text_length' :'8','max_text_length':'8'},
            inputType: "text"
        }
    }
    var numeroDeIdentificacionAnotherPickerPath = "checkout.steps.store-pickup.store-selector.another-picker.numero_identificacion_picker";
    return Select.extend({
        defaults: {
            caption: false
        },
        /**
         * Initialize component identificacion
         * @returns {*}
         * */
        initialize: function () {
            this._super();
            this.caption(null);
            this.value.subscribe(function (value) {
                var numeroDeIdentificacionAnotherPicker = registry.get(numeroDeIdentificacionAnotherPickerPath);
                var inputConfiguration = this.getInputConfigurationsById(value);
                if (!_.isUndefined(numeroDeIdentificacionAnotherPicker)){
                    numeroDeIdentificacionAnotherPicker.placeholder(inputConfiguration.placeholder);
                    numeroDeIdentificacionAnotherPicker.dataType(inputConfiguration.inputType);
                    numeroDeIdentificacionAnotherPicker.validation = inputConfiguration.validations;
                    numeroDeIdentificacionAnotherPicker.value("");
                    if(_.isEmpty(numeroDeIdentificacionAnotherPicker.value())){
                        numeroDeIdentificacionAnotherPicker.value("");
                    }
                }
            },this);
            var numeroDeIdentificacionAnotherPicker = registry.get(numeroDeIdentificacionAnotherPickerPath);
            var inputConfiguration = this.getInputConfigurationsById('11174');
            if (!_.isUndefined(numeroDeIdentificacionAnotherPicker)){
                numeroDeIdentificacionAnotherPicker.placeholder(inputConfiguration.placeholder);
                numeroDeIdentificacionAnotherPicker.dataType(inputConfiguration.inputType);
                numeroDeIdentificacionAnotherPicker.validation = inputConfiguration.validations;
                if(_.isEmpty(numeroDeIdentificacionAnotherPicker.value())){
                    numeroDeIdentificacionAnotherPicker.value("");
                }
            }
            this.value("11174");
            return this;
        },

        /**
         * Object placeholder lookups
         * @param optionId
         * @returns {*|{inputType: string, placeholder: string, validations: {"required-entry": boolean, min_text_length: string, max_text_length: string}}}
         */
        getInputConfigurationsById: function (optionId) {
            return inputConfiguration[optionId] ? inputConfiguration[optionId] : inputConfiguration['default'];
        }
    });
});
