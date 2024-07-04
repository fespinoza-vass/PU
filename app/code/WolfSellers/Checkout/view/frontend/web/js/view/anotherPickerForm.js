define([
    'ko',
    'Magento_Ui/js/form/form'
], function (
    ko,
    Form
) {
    'use strict';

    return Form.extend({
        initialize: function () {
          this._super();
        },
        /** @inheritdoc */
        initConfig: function () {
            this._super();
            this.namespace = "anotherPicker";
            this.selector = '[data-form-part=' + this.namespace + ']';
            return this;
        },
        /**
         * Validate anotherPicker form by uiComponents method
         * @returns {boolean}
         */
        validateAnotherPickerForm: function () {
            this.source.set('params.anotherPicker', false);
            this.triggerValidationCustomerDataForm();
            return !this.source.get('params.anotherPicker');
        },
        /**
         * Trigger Customer data Step data validate event.
         */
        triggerValidationCustomerDataForm: function () {
            this.source.trigger('anotherPicker.identificacion_picker.data.validate'); // Disparar validación
            this.source.trigger('anotherPicker.numero_identificacion_picker.data.validate'); // Disparar validación
            this.source.trigger('anotherPicker.nombre_completo_picker.data.validate'); // Disparar validación
            this.source.trigger('anotherPicker.email_picker.data.validate'); // Disparar validación
        },
    });
})
