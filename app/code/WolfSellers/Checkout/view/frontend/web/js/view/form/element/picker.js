define([
    'ko',
    "Magento_Ui/js/form/element/select",
    'uiRegistry'
],function (
    ko,
    Component,
    registry
) {
    'use strict';

    return Component.extend({
        options: [
            { value: 'me', label: 'Yo' },
            { value: 'other', label: 'Otra persona' }
        ],
        value: ko.observable(),
        initialize: function () {
            this._super();
            this.validation = {"required-entry": true};
            this.value.subscribe(function (value) {
                if (value === "other"){
                    this.setInputValidation(true);
                }else{
                    this.setInputValidation(false);
                }
            },this);
        },
        /**
         * Set input validation for inputs in another picker area
         * @param value
         */
        setInputValidation: function (value) {
            var identificacion_picker = registry.get("checkout.steps.store-pickup.store-selector.another-picker.identificacion_picker");
            var nombre_completo_picker = registry.get("checkout.steps.store-pickup.store-selector.another-picker.nombre_completo_picker");
            identificacion_picker.value.notifySubscribers('11174');
            nombre_completo_picker.validation = {"required-entry":value}
        }
    });

});
