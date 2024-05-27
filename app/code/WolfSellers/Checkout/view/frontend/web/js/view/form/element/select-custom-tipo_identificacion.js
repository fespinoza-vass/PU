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
            '865': {
                placeholder: 'No. Pasaporte',
                validations: {"validate-identificacion-pasaporte":true,'required-entry':true,'min_text_length':'8','max_text_length':'8',"validate-number": true},
                inputType: "number"
            },
            '868': {
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
    var componentName = "checkout.steps.customer-data-step.customer-fieldsets.customer-data-numero_de_identificacion";
    return Select.extend({
        /**
         * Initialize component identificacion
         * @returns {*}
         * */
        initialize: function () {
            this._super();
            this.value.subscribe(function (value) {
                var cedulaComponent = registry.get(componentName);
                var inputConfiguration = this.getInputConfigurationsById(value);
                if (!_.isUndefined(cedulaComponent)){
                    cedulaComponent.placeholder(inputConfiguration.placeholder);
                    cedulaComponent.dataType(inputConfiguration.inputType);
                    cedulaComponent.validation = inputConfiguration.validations;
                    if(_.isEmpty(cedulaComponent.value())){
                        cedulaComponent.value("");
                    }
                }
            },this);
            var cedulaComponent = registry.get(componentName);
            var inputConfiguration = this.getInputConfigurationsById('868');
            if (!_.isUndefined(cedulaComponent)){
                cedulaComponent.placeholder(inputConfiguration.placeholder);
                cedulaComponent.dataType(inputConfiguration.inputType);
                cedulaComponent.validation = inputConfiguration.validations;
                if(_.isEmpty(cedulaComponent.value())){
                    cedulaComponent.value("");
                }
            }
            if(_.isEmpty(this.value())){
                this.value("868");
            }
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
